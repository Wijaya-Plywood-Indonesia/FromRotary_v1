<?php

namespace App\Filament\Resources\HargaKayus\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
                TextColumn::make('jenisKayu.nama_kayu')
                    ->sortable(),
                TextColumn::make('panjang')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('diameter_terkecil')
                    ->label('Min')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('diameter_terbesar')
                    ->label('Max')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label('A / B')
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        1 => 'Grade A',
                        2 => 'Grade B',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn($state) => match ((int) $state) {
                        1 => 'success',
                        2 => 'primary',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([

                ViewAction::make(),
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
