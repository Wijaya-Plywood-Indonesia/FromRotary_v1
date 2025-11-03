<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DetailKayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_kayu_masuk')
                    ->numeric(),
                TextInput::make('diameter')
                    ->required()
                    ->numeric(),
                TextInput::make('panjang')
                    ->required()
                    ->numeric(),
                TextInput::make('grade')
                    ->required()
                    ->numeric(),
                Textarea::make('keterangan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
