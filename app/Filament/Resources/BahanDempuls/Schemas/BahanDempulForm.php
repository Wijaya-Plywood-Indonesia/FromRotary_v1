<?php

namespace App\Filament\Resources\BahanDempuls\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BahanDempulForm
{

    public static function getBahanOptions(): array
    {
        return [
            'kalsium' => 'Kalsium (gram)',
            'semen' => 'Semen (kg)',
            'lem_pvac' => 'Lem PVAC (gr)',
            'tepung_anggrek' => 'Tepung Anggrek (kg)',
        ];
    }
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nama_bahan')
                    ->label('Nama Bahan')
                    // Menggunakan method static untuk options
                    ->options(self::getBahanOptions())
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
