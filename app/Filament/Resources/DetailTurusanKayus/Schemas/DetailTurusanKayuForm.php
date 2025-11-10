<?php

namespace App\Filament\Resources\DetailTurusanKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailTurusanKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomer_urut')
                    ->required()
                    ->numeric(),
                TextInput::make('lahan_id')
                    ->numeric(),
                TextInput::make('jenis_kayu_id')
                    ->numeric(),
                TextInput::make('panjang')
                    ->required()
                    ->numeric(),
                TextInput::make('grade')
                    ->required()
                    ->numeric(),
                TextInput::make('diameter')
                    ->required()
                    ->numeric(),
                TextInput::make('kuantitas')
                    ->required()
                    ->numeric(),
            ]);
    }
}
