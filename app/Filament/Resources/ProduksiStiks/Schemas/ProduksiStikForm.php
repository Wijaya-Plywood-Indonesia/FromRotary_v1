<?php

namespace App\Filament\Resources\ProduksiStiks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ProduksiStikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_produksi')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()->addDay()) // 👈 default besok
                    ->displayFormat('d F Y') // 👈 tampil seperti: 01 Januari 2025
                    ->required(),
            ]);
    }
}
