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
                    // Format ini bagus untuk menampilkan kode dan nama
                    ->formatStateUsing(fn($record) => "{$record->pegawai->kode_pegawai} - {$record->pegawai->nama_pegawai}")
                    ->searchable(['kode_pegawai', 'nama_pegawai']), // Tambahkan search ke relasi

                // Tambahkan kolom untuk Kayu Masuk (Seri)
                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri Kayu Masuk')
                    ->searchable()
                    ->sortable(),

                // Anda juga bisa tambahkan relasi dari kayuMasuk
                TextColumn::make('kayuMasuk.penggunaanSupplier.nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default

                TextColumn::make('kayuMasuk.penggunaanKendaraanSupplier.nopol_kendaraan')
                    ->label('Nopol')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([ // Mengganti toolbarActions dengan bulkActions
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}