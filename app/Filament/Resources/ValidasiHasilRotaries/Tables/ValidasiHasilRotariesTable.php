<?php

namespace App\Filament\Resources\ValidasiHasilRotaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ValidasiHasilRotariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_produksi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('timestamp_laporan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('id_ukuran')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kw')
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
