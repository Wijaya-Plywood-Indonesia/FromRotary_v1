<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_turun_kayu')
                    ->numeric(),
                TextInput::make('id_pegawai')
                    ->numeric(),
            ]);
    }
}
