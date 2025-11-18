<?php

namespace App\Filament\Resources\PegawaiRepairs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PegawaiRepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_pegawai')
                    ->required()
                    ->numeric(),
                TimePicker::make('jam_masuk'),
                TimePicker::make('jam_pulang'),
                TextInput::make('izin'),
                TextInput::make('keterangan'),
            ]);
    }
}
