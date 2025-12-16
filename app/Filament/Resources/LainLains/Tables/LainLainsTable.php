<?php

namespace App\Filament\Resources\LainLains\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LainLainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai')
                    ->label('Pegawai')
                    ->getStateUsing(
                        fn($record) =>
                        $record->pegawai
                            ? "{$record->pegawai->kode_pegawai} - {$record->pegawai->nama_pegawai}"
                            : '-'
                    )
                    ->sortable()
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
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('hasil')
                    ->label('Hasil')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: false),
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
