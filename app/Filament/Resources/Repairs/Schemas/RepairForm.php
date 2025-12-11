<?php

namespace App\Filament\Resources\Repairs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class RepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),

                TextInput::make('jumlah_meja')
                    ->label('Jumlah Meja')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->rules(['integer', 'min:1']),
            ]);
    }
}
