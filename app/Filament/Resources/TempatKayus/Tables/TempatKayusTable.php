<?php

namespace App\Filament\Resources\TempatKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TempatKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jumlah_batang')
                    ->label('Jumlah Batang')
                    ->sortable(),
                TextColumn::make('poin')
                    ->label('Poin')
                    ->money('Rp.'),

                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri Kayu')
                    ->formatStateUsing(fn($state) => 'Seri - ' . ($state ?? '-'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('lahan.kode_lahan')
                    ->label('Kode Lahan')
                    ->sortable()
                    ->searchable(),
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
