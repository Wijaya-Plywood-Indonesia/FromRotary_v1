<?php

namespace App\Filament\Resources\DetailTurunKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailTurunKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Nama Pegawai')
                    ->formatStateUsing(fn($record) => "{$record->pegawai->kode_pegawai} - {$record->pegawai->nama_pegawai}"),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Petugas Turun Kayu'), //++ˆ ini yang munculkan tombol "Tambah"
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
