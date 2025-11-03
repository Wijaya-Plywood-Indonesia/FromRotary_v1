<?php

namespace App\Filament\Resources\DetailHasilPaletRotaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailHasilPaletRotariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('timestamp_laporan')
                    ->label('Waktu Laporan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('lahan_display')
                    ->label('Lahan')
                    ->getStateUsing(
                        fn($record) =>
                        "{$record->lahan->kode_lahan} - {$record->lahan->nama_lahan}"
                    )
                    ->sortable(['lahan.kode_lahan']) // optional
                    ->searchable(['lahan.kode_lahan', 'lahan.nama_lahan']),

                TextColumn::make('setoranPaletUkuran.dimensi')
                    ->label('Ukuran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kw')
                    ->searchable(),

                TextColumn::make('id_penggunaan_lahan')
                    ->searchable(),

                TextColumn::make('total_lembar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(), // 👈 ini yang munculkan tombol "Tambah"
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
