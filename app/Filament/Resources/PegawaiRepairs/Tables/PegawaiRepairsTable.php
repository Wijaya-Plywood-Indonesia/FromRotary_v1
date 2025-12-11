<?php

namespace App\Filament\Resources\PegawaiRepairs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
class PegawaiRepairsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Textcolumn::make('pegawai.kode_pegawai')
                    ->label('Kode Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->dateTime('H:i'),
                TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->dateTime('H:i'),
                TextColumn::make('ijin')
                    ->label('Ijin')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nomor_meja')
                    ->label('Nomor Meja')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->emptyStateHeading('Belum ada data pegawai perbaikan')
            ->emptyStateDescription('Tambahkan pegawai yang mengerjakan perbaikan meja.');
    }
}
