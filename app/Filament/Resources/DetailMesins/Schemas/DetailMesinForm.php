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
                    ->options(
                        Mesin::whereHas('kategoriMesin', function ($query) {
                            $query->where('nama_kategori_mesin', 'DRYER');
                        })
                            ->orderBy('nama_mesin')
                            ->pluck('nama_mesin', 'id')
                    )
                    ->searchable()
                    ->required(),

                TextInput::make('jam_kerja_mesin')
                    ->default(12)        // otomatis 12
                    ->hidden()           // tidak tampil di form
                    ->dehydrated(),    // tetap disimpan ke database
            ]);
    }
}
