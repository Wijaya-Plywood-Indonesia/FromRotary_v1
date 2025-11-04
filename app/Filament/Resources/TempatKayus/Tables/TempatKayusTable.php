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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('poin')
                    ->money('Rp')
                    ->numeric(),
                TextColumn::make('id_kayu_masuk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id_lahan')
                    ->searchable()
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
