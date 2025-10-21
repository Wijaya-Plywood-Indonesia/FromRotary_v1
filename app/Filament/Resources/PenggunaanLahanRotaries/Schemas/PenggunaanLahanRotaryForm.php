<?php

namespace App\Filament\Resources\PenggunaanLahanRotaries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenggunaanLahanRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_lahan')
                    ->required()
                    ->numeric(),
                TextInput::make('id_produksi')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah_batang')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
