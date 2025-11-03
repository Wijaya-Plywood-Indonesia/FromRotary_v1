<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DetailKayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('diameter (cm)')
                    ->placeholder('13 cm - 50 cm')
                    ->required()
                    ->numeric(),
                Select::make('panjang')
                    ->label('Panjang')
                    ->options([
                        130 => '130 cm',
                        260 => '260 cm',
                    ])
                    ->required()
                    ->native(false),

                Select::make('grade')
                    ->label('Grade')
                    ->options([
                        1 => 'Grade A',
                        2 => 'Grade B',
                    ])
                    ->required()
                    ->native(false) // biar tampilannya lebih bagus (dropdown Filament)
                    ->searchable(), // opsional, kalau mau bisa dicari
                Textarea::make('keterangan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
