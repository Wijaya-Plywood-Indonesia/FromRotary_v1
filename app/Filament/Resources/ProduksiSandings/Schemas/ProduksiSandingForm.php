<?php

namespace App\Filament\Resources\ProduksiSandings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProduksiSandingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->required(),
                TextInput::make('kendala'),
            ]);
    }
}
