<?php

namespace App\Filament\Resources\DetailDempuls\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class DetailDempulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /**
             * =====================================
             * OPTIMASI QUERY (WAJIB)
             * =====================================
             */
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with([
                    'pegawais',
                    'barangSetengahJadi.ukuran',
                    'barangSetengahJadi.jenisBarang',
                    'barangSetengahJadi.grade.kategoriBarang',
                ])
            )

            /**
             * =====================================
             * DEFAULT GROUP BY PEGAWAI
             * =====================================
             */
            ->groups([
                Group::make('id') // ⚠️ HARUS KOLOM ASLI
                    ->label('Pegawai')
                    ->getTitleFromRecordUsing(function ($record) {
                        if ($record->pegawais->isEmpty()) {
                            return 'Pegawai: -';
                        }

                        return 'Pegawai: ' .
                            $record->pegawais
                                ->pluck('nama_pegawai')
                                ->implode(' & ');
                    })
                    ->collapsible(),
            ])

            // ⬇️ INI YANG MEMBUAT DEFAULT LANGSUNG KELOMPOK
            ->defaultGroup('id')

            /**
             * =====================================
             * COLUMNS
             * =====================================
             */
            ->columns([
                TextColumn::make('barang')
                    ->label('Barang')
                    ->getStateUsing(function ($record) {
                        $b = $record->barangSetengahJadi;
                        if (!$b) return '-';

                        return
                            ($b->grade?->kategoriBarang?->nama_kategori ?? '-') . ' | ' .
                            ($b->ukuran?->nama_ukuran ?? '-') . ' | ' .
                            ($b->grade?->nama_grade ?? '-') . ' | ' .
                            ($b->jenisBarang?->nama_jenis_barang ?? '-');
                    })
                    ->wrap(),

                TextColumn::make('modal')
                    ->numeric(),

                TextColumn::make('hasil')
                    ->numeric()
                    ->color(fn ($record) =>
                        $record->hasil < $record->modal ? 'danger' : 'success'
                    ),

                TextColumn::make('nomor_palet')
                    ->numeric(),
            ])

            /**
             * =====================================
             * ACTIONS
             * =====================================
             */
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
