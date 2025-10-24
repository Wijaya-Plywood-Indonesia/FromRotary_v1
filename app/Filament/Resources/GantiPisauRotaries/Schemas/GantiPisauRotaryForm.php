<?php

namespace App\Filament\Resources\GantiPisauRotaries\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class GantiPisauRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TimePicker::make('jam_mulai_ganti_pisau')
                    ->required()
                    ->default(fn() => now()->format('H:i')),

                TimePicker::make('jam_selesai_ganti')
                    ->required()
                    ->default(fn() => now()->format('H:i')),
            ]);
    }
}
