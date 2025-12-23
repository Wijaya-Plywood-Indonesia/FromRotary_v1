<?php

namespace App\Filament\Resources\DetailDempuls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailDempulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_produksi_dempul')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('id_rencana_pegawai_dempul')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('id_barang_setengah_jadi_hp')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('modal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hasil')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nomor_palet')
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
