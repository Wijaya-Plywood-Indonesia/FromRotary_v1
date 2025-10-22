<?php

namespace App\Filament\Resources\PenggunaanLahanRotaries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenggunaanLahanRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_lahan')
                    ->label('Lahan')
                    ->relationship('lahan', 'kode_lahan')
                    ->searchable()
                    ->required(),
                Select::make('id_produksi')
                    ->label('Mesin Produksi')
                    ->relationship('produksi_rotary', 'id') // nama relasi di model + kolom yang ditampilkan
                    ->required(),
                TextInput::make('jumlah_batang')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
