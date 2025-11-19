<?php

namespace App\Filament\Resources\ValidasiKedis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ValidasiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_produksi_kedi')
                    ->required()
                    ->numeric(),
                TextInput::make('role')
                    ->required(),
                TextInput::make('status')
                    ->required(),
            ]);
    }
}
