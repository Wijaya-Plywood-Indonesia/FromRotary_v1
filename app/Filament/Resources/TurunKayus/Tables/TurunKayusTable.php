<?php

namespace App\Filament\Resources\TurunKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TurunKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kayuMasuk')
                    ->label('Kayu & Kendaraan')
                    ->formatStateUsing(function ($record) {
                        $kayu = $record->kayuMasuk;
                        $kendaraan = $kayu?->penggunaanKendaraanSupplier;
                        return "Kayu Seri {$kayu?->seri} - {$kendaraan?->nopol_kendaraan} ({$kendaraan?->jenis_kendaraan})";
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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
