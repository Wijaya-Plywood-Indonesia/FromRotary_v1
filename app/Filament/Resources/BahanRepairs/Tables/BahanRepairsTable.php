<?php

namespace App\Filament\Resources\BahanRepairs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;

class BahanRepairsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ukuran.dimensi')
                    ->label('Ukuran')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jenisKayu.kode_kayu')
                    ->label('Kode Kayu')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kw')
                    ->label('KW')
                    ->formatStateUsing(fn($state) => $state ?? '-') // tampilkan '-' kalau kosong
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_lembar')
                    ->label('Lembar')
                    ->numeric()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
