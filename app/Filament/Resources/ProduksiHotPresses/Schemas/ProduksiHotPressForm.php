<?php

namespace App\Filament\Resources\ProduksiHotPresses\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms;

class ProduksiHotPressForm
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
