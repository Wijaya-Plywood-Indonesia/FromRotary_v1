<?php

namespace App\Filament\Resources\Ukurans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UkuranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('panjang')
                    ->required(),
                TextInput::make('lebar')
                    ->required(),
                TextInput::make('tebal')
                    ->required(),
            ]);
    }
}
