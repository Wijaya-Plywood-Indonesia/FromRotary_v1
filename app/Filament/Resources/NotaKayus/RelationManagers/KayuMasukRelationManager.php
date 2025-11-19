<?php

namespace App\Filament\Resources\NotaKayus\RelationManagers;

use App\Models\JenisKayu;
use App\Models\Lahan;
use App\Services\KayuComparator;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KayuMasukRelationManager extends RelationManager
{
    protected static string $relationship = 'kayuMasuk';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return 'Perbandingan Detail & Turusan';
    }

    public function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('selisih')
                    ->label('Selisih')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->selisih == 0 ? 'Sama' : 'Selisih ' . $record->selisih
                    )
                    ->orderQueryUsing(
                        fn(Builder $query, string $direction) =>
                        $query->orderBy('selisih', 'desc')   // <-- selalu DESC
                    )
                    ->collapsible(),

            ])
            ->defaultGroup('selisih')
            ->defaultSort('selisih', 'desc')

            ->query(function () {
                $owner = $this->getOwnerRecord();
                return KayuComparator::buildQuery($owner->id);
            })
            ->columns([
                TextColumn::make('info_kayu')
                    ->label('Info')
                    ->getStateUsing(function ($record) {
                        $namaKayu = JenisKayu::find($record->id_jenis_kayu)->nama_kayu ?? '-';
                        $kodeLahan = Lahan::find($record->id_lahan)->kode_lahan ?? '-';
                        $panjang = $record->panjang ? "{$record->panjang} cm" : '-';

                        return "{$namaKayu} - {$kodeLahan} - {$panjang}";
                    })
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('diameter')
                    ->sortable()
                    ->suffix(' cm'),


                TextColumn::make('detail_jumlah')->label('Turus 1'),
                TextColumn::make('turusan_jumlah')->label('Turuss 2'),
                TextColumn::make('grade')->label('Grade'),

                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return 'Sama';
                        }
                        return 'Selisih ' . $state;
                    })
                    ->color(fn($state) => $state == 0 ? 'gray' : 'danger')
                    ->icon(fn($state) => $state == 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle'),

            ]);
    }
}
