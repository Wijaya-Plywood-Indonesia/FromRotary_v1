<?php

namespace App\Filament\Resources\ProduksiKedis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProduksiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()) // 👈 default besok
                    ->displayFormat('d M Y') // 👈 tampil seperti: 01 Januari 2025
                    ->required(),

                Select::make('status')
                    ->options(['bongkar' => 'Bongkar', 'masuk' => 'Masuk'])
                    ->default('bongkar')
                    ->required(),

                Select::make('kode_kedi')
                    ->options([
                        'Kedi 1' => 'Kedi 1',
                        'Kedi 2' => 'Kedi 2',
                        'Kedi 3' => 'Kedi 3',
                        'Kedi 4' => 'Kedi 4',
                    ])
                    ->default('Kedi 1')
                    ->required(),
            ]);
    }
}
