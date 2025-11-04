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
                TextColumn::make('id_kayu_masuk')
                    ->label('Id Kayu Masuk')
                    ->sortable(),
                TextColumn::make('id_lahan')
                    ->label('Id Lahan')
                    ->sortable()
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
