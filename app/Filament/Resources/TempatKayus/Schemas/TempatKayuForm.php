<?php

namespace App\Filament\Resources\TempatKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class TempatKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jumlah_batang')
                    ->required()
                    ->numeric(),
                TextInput::make('poin')
                    ->required()
                    ->numeric(),
                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk')
                    ->relationship('kayuMasuk', 'seri')
                    ->searchable()
                    ->preload(),
                // ->required(),
                Select::make('id_lahan')
                    ->label('Lahan')
                    ->relationship('lahan', 'nama_lahan')
                    ->searchable()
                    ->preload()
                    ->required()
            ]);
    }
}
