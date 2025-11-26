<?php

namespace App\Filament\Resources\RencanaPegawais\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class RencanaPegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pegawai')
                    ->required()
                    ->numeric(),
                TextInput::make('nomor_meja')
                    ->required()
                    ->numeric(),
            ]);
    }
}
