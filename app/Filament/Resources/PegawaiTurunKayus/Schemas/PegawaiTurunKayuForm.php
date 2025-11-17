<?php

namespace App\Filament\Resources\PegawaiTurunKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Pegawai;

class PegawaiTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'nama_pegawai')
                    ->searchable()
                    ->required(),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->required(),
                TimePicker::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->required(),
            ]);
    }
}