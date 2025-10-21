<?php

namespace App\Filament\Resources\Mesins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MesinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kategori_mesin_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nama_mesin')
                    ->required(),
                TextInput::make('ongkos_mesin')
                    ->required()
                    ->numeric(),
                TextInput::make('no_akun')
                    ->required(),
                Textarea::make('detail_mesin')
                    ->columnSpanFull(),
            ]);
    }
}
