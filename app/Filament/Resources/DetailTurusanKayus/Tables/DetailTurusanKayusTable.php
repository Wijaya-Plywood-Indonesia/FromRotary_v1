<?php

namespace App\Filament\Resources\DetailTurusanKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailTurusanKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomer_urut')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lahan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_kayu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('panjang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('diameter')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kuantitas')
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
            ->headerActions([
                CreateAction::make(),

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
