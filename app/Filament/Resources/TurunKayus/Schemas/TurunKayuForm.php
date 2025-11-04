<?php

namespace App\Filament\Resources\TurunKayus\Schemas;

use App\Models\KayuMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->placeholder('Pilih Tanggal')
                    ->default(now())
                    ->required(),

                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk')
                    ->options(
                        KayuMasuk::with('penggunaanKendaraanSupplier') // eager load biar hemat query
                            ->get()
                            ->mapWithKeys(function ($kayu) {
                                $kendaraan = $kayu->penggunaanKendaraanSupplier;
                                $nopol = $kendaraan?->nopol_kendaraan ?? '-';
                                $jenis = $kendaraan?->jenis_kendaraan ?? '-';

                                return [
                                    $kayu->id => "Seri {$kayu->seri} - {$nopol} ({$jenis})",
                                ];
                            })
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih Kayu Masuk'),


            ]);
    }
}
