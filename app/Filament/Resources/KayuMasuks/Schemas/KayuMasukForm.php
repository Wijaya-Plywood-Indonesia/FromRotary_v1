<?php

namespace App\Filament\Resources\KayuMasuks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis_Dokumen_angkut')
                    ->label('Jenis Dokumen Legal')
                    ->options([
                        'SAKR' => 'SAKR',
                        '' => 'Letter C',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('upload_dokumen_angkut')
                    ->required(),
                DateTimePicker::make('tgl_kayu_masuk')
                    ->required(),
                TextInput::make('seri')
                    ->required()
                    ->numeric(),
            ]);
    }
}
