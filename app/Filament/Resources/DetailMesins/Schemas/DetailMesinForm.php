<?php

namespace App\Filament\Resources\DetailMesins\Schemas;

use Filament\Schemas\Schema;
use App\Models\KategoriMesin;
use App\Models\Mesin;
use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get; // ✅ Ganti import ke ini (tanpa Components)
use Closure; // Opsional: Tambahkan ini jika PHP butuh tipe hint Closure

class DetailMesinForm
{
    
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('id_mesin_dryer')
                    ->label('Mesin Dryer')
                    // ... (pengaturan lainnya)
                    ->searchable()
                    ->required(),

                TextInput::make('jam_kerja_mesin')
                    // ->default(function (Get $get): ?int { // ✅ Gunakan Get $get
                    //     // Path relatif: keluar dari field, keluar dari item repeater, ke form utama
                    //     $shift = $get('../../shift'); 
                        
                    //     if ($shift === 'PAGI') {
                    //         return 11;
                    //     }
                        
                    //     return 12; 
                    // })
                    ->hidden()          
                    ->dehydrated(),    
            ]);
    }
}