<?php

namespace App\Filament\Resources\PegawaiRotaries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PegawaiRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_produksi')
                    ->required()
                    ->numeric(),
                TextInput::make('id_pegawai')
                    ->required()
                    ->numeric(),
                TextInput::make('role'),
                TimePicker::make('jam_masuk')
                    ->required(),
                TimePicker::make('jam_pulang')
                    ->required(),
            ]);
    }
}
