<?php

namespace App\Filament\Resources\RiwayatKayus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class RiwayatKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_masuk')
                    ->native(false)
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y'),
                DatePicker::make('tanggal_digunakan')
                    ->native(false)
                    ->default(now()->startOfMonth())
                    ->required()
                    ->displayFormat('d/m/Y'),
                DatePicker::make('tanggal_habis')
                    ->native(false)
                    ->default(now()->startOfMonth())
                    ->required()
                    ->displayFormat('d/m/Y'),
                Select::make('id_tempat_kayu')
                    ->label('Tempat Kayu')
                    ->relationship('tempatKayu', 'jumlah_batang')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
