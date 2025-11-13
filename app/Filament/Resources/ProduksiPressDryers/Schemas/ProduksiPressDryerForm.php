<?php

namespace App\Filament\Resources\ProduksiPressDryers\Schemas;

use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProduksiPressDryerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tgl_produksi')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()->addDay()) // 👈 default besok
                    ->displayFormat('d F Y') // 👈 tampil seperti: 01 Januari 2025
                    ->required(),
            ]);
    }
}
