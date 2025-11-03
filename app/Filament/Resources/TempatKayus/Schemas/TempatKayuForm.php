<?php

namespace App\Filament\Resources\TempatKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TempatKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('poin')
                    ->required(),
                TextInput::make('id_kayu_masuk')
                    ->required()
            ]);
    }
}
