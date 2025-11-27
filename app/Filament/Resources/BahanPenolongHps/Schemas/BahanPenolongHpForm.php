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
                        'lem_pai' => 'Lem Pai',
                        'lem_dover' => 'Lem Dover',
                        'air' => 'Air',
                        'tepung_bgs' => 'Tepung BGS',
                        'tepung_pjp' => 'Tepung PJP',
                        'isi_steples' => 'Isi Steples',
                        'solasi_putih' => 'Solusi Putih',
                        'pewarna' => 'Pewarna',
                    ])
                
                    ->required()
                    ->native(false)
                    ->searchable(),

                TextInput::make('isi')
                    ->label('Banyak')
                    ->required()
                    ->numeric()
                    ->placeholder('gr/kg/pack/roll'),
            ]);
    }
}
