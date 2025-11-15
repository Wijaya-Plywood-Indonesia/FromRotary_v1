<?php

namespace App\Filament\Resources\PegawaiDryers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class PegawaiDryersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tugas')
                    ->label('Tugas')
                    ->searchable(),

                TextColumn::make('masuk')
                    ->label('Masuk')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('pulang')
                    ->label('Pulang')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('ijin')
                    ->label('Ijin')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('ket')
                    ->label('Keterangan')
                    ->limit(30)
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
