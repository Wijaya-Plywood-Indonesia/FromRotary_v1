<?php

namespace App\Filament\Resources\DetailBongkarKedis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailBongkarKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_palet')
                    ->required()
                    ->numeric(),
                TextInput::make('id_jenis_kayu')
                    ->required()
                    ->numeric(),
                TextInput::make('id_ukuran')
                    ->required()
                    ->numeric(),
                TextInput::make('kw')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                TextInput::make('id_produksi_kedi')
                    ->required()
                    ->numeric(),
            ]);
    }
}
