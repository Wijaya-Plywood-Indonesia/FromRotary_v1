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
                    ->label('Panjang (cm)')
                    ->required(),
                TextInput::make('lebar')
                    ->label('Lebar (cm)')
                    ->required(),
                TextInput::make('tebal')
                    ->label('Tebal (cm)')
                    ->required(),
            ]);
    }
}
