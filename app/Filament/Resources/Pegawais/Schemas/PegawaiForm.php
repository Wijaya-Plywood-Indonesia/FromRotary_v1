<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_pegawai')
                    ->required(),
                TextInput::make('nama_pegawai')
                    ->required(),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                TextInput::make('no_telepon_pegawai')
                    ->tel(),
                Toggle::make('jenis_kelamin_pegawai')
                    ->required(),
                DatePicker::make('tanggal_masuk')
                    ->required(),
                TextInput::make('foto'),
            ]);
    }
}
