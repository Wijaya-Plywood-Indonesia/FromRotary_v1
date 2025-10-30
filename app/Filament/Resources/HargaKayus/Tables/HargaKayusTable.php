<?php

namespace App\Filament\Resources\HargaKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HargaKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('panjang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('diameter_terkecil')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('diameter_terbesar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga_beli')
                    ->label('Harga Beli Per batang')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('id_jenis_kayu')
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
