<?php

namespace App\Filament\Resources\RiwayatKayus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiwayatKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_masuk')
                    ->format('d/m/Y'),
                DatePicker::make('tanggal_digunakan')
                    ->format('d/m/Y'),
                DatePicker::make('tanggal_habis')
                    ->format('d/m/Y'),
                TextInput::make('id_tempat_kayu')
                    ->required()
            ]);
    }
}
