<?php

namespace App\Filament\Resources\BahanPenolongHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BahanPenolongHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nama_bahan')
                    ->label('Nama Bahan')
                    ->options([
                        'lem_pai' => 'Lem Pai (kg)',
                        'lem_dover' => 'Lem Dover (kg)',
                        'air' => 'Air',
                        'hdr' => 'HDR (gr)',
                        'tepung_bgs' => 'Tepung BGS (kg)',
                        'tepung_pjp' => 'Tepung PJP (kg)',
                        'isi_steples' => 'Isi Steples (pack)',
                        'solasi_putih' => 'Solasi Putih (roll)',
                        'pewarna' => 'Pewarna (gr)',
                    ])
                
                    ->required()
                    ->native(false)
                    ->searchable(),

                TextInput::make('jumlah')
                    ->label('Banyak')
                    ->required()
                    ->numeric(),
            ]);
    }
}
