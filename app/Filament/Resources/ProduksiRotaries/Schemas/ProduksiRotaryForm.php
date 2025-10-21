<?php

namespace App\Filament\Resources\ProduksiRotaries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProduksiRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_mesin')
                    ->required()
                    ->numeric(),
                DatePicker::make('tgl_produksi')
                    ->required(),
                Textarea::make('kendala')
                    ->columnSpanFull(),
            ]);
    }
}
