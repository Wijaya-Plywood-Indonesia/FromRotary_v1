<?php

namespace App\Filament\Resources\GantiPisauRotaries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class GantiPisauRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_produksi')
                    ->required()
                    ->numeric(),
                TimePicker::make('jam_mulai_ganti_pisau')
                    ->required(),
                TimePicker::make('jam_selesai_ganti')
                    ->required(),
            ]);
    }
}
