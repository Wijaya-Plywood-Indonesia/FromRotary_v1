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

                TextColumn::make('kayuMasuk.tgl_kayu_masuk')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),


                TextColumn::make('kayuMasuk.penggunaanSupplier.nama_supplier')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),


                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri')
                    ->sortable(),

                TextColumn::make('panjang')
                    ->label('Panjang')
                    ->sortable(),


                TextColumn::make('jenisKayu.nama_kayu')
                    ->label('Jenis')
                    ->sortable(),


                TextColumn::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->sortable(),

                TextColumn::make('kuantitas')
                    ->label('Banyak')
                    ->numeric()
                    ->summarize(Sum::make()),

                TextColumn::make('diameter')
                    ->label('D')
                    ->numeric()
                ,

            ])
            ->defaultSort('kayuMasuk.tgl_kayu_masuk', 'desc');
    }
}
