<?php

namespace App\Filament\Resources\ProduksiPressDryers\Schemas;

use App\Models\ProduksiPressDryer;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
class ProduksiPressDryerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_produksi')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()->addDay())
                    ->displayFormat('d F Y')
                    ->required(),
                Select::make('shift')
                    ->label('Shift')
                    ->options([
                        'PAGI' => 'Shift Pagi',
                        'MALAM' => 'Shift Malam',
                    ])
                    ->searchable()
                    ->required(),
            ]);
    }
}
