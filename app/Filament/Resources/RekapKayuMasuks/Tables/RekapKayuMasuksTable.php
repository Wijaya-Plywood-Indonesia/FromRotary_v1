<?php

namespace App\Filament\Resources\RekapKayuMasuks\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

class RekapKayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ============================
                // 1. TANGGAL (dari kayu masuk)
                // ============================
                TextColumn::make('kayuMasuk.tgl_kayu_masuk')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                // ======================
                // 2. NAMA SUPPLIER
                // ======================
                TextColumn::make('kayuMasuk.penggunaanSupplier.nama_supplier')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                // ==============
                // 3. SERI
                // ==============
                TextColumn::make('seri')
                    ->label('Seri')
                    ->sortable(),

                // ======================
                // 4. PANJANG
                // ======================
                TextColumn::make('panjang')
                    ->label('Panjang')
                    ->sortable(),

                // ======================
                // 5. JENIS KAYU
                // ======================
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->sortable(),

                // ======================
                // 6. LAHAN
                // ======================
                TextColumn::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->sortable(),

                // ======================
                // 7. BANYAK / PCS
                // ======================
                TextColumn::make('banyak')
                    ->label('Banyak')
                    ->numeric()
                    ->summarize(Sum::make()),

                // ======================
                // 8. M3 / KUBIKASI
                // ======================
                TextColumn::make('m3')
                    ->label('M3')
                    ->numeric(3)
                    ->summarize(Sum::make()),

                // ======================
                // 9. POIN
                // ======================
                TextColumn::make('poin')
                    ->label('Poin')
                    ->numeric()
                    ->summarize(Sum::make()),

            ])
            ->defaultSort('kayuMasuk.tgl_kayu_masuk', 'desc');
    }
}
