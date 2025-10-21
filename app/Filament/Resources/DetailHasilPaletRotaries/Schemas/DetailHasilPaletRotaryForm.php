<?php

namespace App\Filament\Resources\DetailHasilPaletRotaries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailHasilPaletRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_produksi')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('timestamp_laporan')
                    ->required(),
                TextInput::make('id_ukuran')
                    ->required()
                    ->numeric(),
                TextInput::make('kw')
                    ->required(),
                TextInput::make('total_lembar')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
