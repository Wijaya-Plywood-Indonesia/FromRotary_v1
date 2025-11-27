<?php

namespace App\Filament\Resources\RencanaRepairs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class RencanaRepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->native(false)                    // modern, responsive
                    ->format('Y-m-d')                     // format penyimpanan
                    ->displayFormat('d/m/Y')             // tampil di UI
                    ->live()
                    ->closeOnDateSelection()
                    ->required()
                    ->maxDate(now()->addDays(30))
                    ->default(now()->addDay())
                    ->suffixIcon('heroicon-o-calendar')
                    ->suffixIconColor('primary'),
            ]);
    }
}
