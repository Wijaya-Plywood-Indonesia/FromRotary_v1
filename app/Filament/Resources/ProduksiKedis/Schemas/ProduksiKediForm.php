<?php

namespace App\Filament\Resources\ProduksiKedis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProduksiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()->addDay()) // 👈 default besok
                    ->displayFormat('d M Y') // 👈 tampil seperti: 01 Januari 2025
                    ->required(),

                Select::make('status')
                    ->options(['bongkar' => 'Bongkar', 'masuk' => 'Masuk'])
                    ->required(),
            ]);
    }
}
