<?php

namespace App\Filament\Resources\RiwayatKayus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class RiwayatKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_masuk')
                    ->native(false)
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y'),
                DatePicker::make('tanggal_digunakan')
                    ->native(false)
                    ->default(now()->startOfMonth())
                    ->required()
                    ->displayFormat('d/m/Y'),
                DatePicker::make('tanggal_habis')
                    ->native(false)
                    ->default(now()->startOfMonth())
                    ->required()
                    ->displayFormat('d/m/Y'),
                Select::make('id_tempat_kayu')
                    ->label('Tempat Kayu')
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\TempatKayu::with('lahan')
                            ->whereHas('lahan', fn($q) => $q->where('nama_lahan', 'like', "%{$search}%"))
                            ->orWhere('jumlah_batang', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($item) => [
                                $item->id => "{$item->lahan->nama_lahan} | {$item->jumlah_batang} batang | {$item->kubikasi} m³"
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
