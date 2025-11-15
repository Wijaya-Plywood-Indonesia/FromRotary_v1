<?php

namespace App\Filament\Resources\DetailMesins\Schemas;

use Filament\Schemas\Schema;
use App\Models\KategoriMesin;
use App\Models\Mesin;
use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DetailMesinForm
{
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('id_mesin_dryer')
                    ->label('Mesin Dryer')
                    // Asumsi: relasi 'mesinDryer' & kolom 'nama' atau 'kode_mesin'
                    ->relationship('mesin', 'nama_mesin')
                    ->searchable()
                    ->preload()
                    ->nullable(), // Sesuai dengan migrasi Anda

                TextInput::make('jam_kerja_mesin')
                    ->default(12)        // otomatis 12
                    ->hidden()           // tidak tampil di form
                    ->dehydrated(),    // tetap disimpan ke database
            ]);
    }
}
