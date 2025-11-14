<?php

namespace App\Filament\Resources\DetailMesins\Tables;

use App\Models\DetailMesin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
class DetailMesinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->query(DetailMesin::query())
            ->columns([
                // MESIN DRYER
                TextColumn::make('mesin.nama_mesin')
                    ->label('Mesin')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->mesin
                        ? $state . ' (' . $record->mesin->kategoriMesin?->nama_kategori_mesin . ')'
                        : '-'
                    )
                    ->searchable()
                    ->sortable(),

                // KATEGORI (OPSIONAL)
                TextColumn::make('kategoriMesin.nama_kategori_mesin')
                    ->label('Kategori')
                    ->placeholder('-')
                    ->searchable(),

                // JAM KERJA
                TextColumn::make('jam_kerja_mesin')
                    ->label('Jam Kerja')
                    ->searchable(),

                // PRODUKSI
                TextColumn::make('produksiDryer.tanggal_produksi')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('produksiDryer.shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn($state) => $state === 'PAGI' ? 'success' : 'warning'),
            ])
            ->filters([
                SelectFilter::make('id_kategori_mesin')
                    ->relationship('kategoriMesin', 'nama_kategori_mesin'),

                SelectFilter::make('id_produksi_dryer')
                    ->relationship('produksiDryer', 'tanggal_produksi')
                    ->getOptionLabelFromRecordUsing(
                        fn($r) =>
                        $r?->tanggal_produksi->format('d M Y') . ' | ' . $r?->shift
                    ),
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
