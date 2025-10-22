<?php

namespace App\Filament\Resources\ProduksiRotaries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProduksiRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_mesin')
                    ->label('Mesin')
                    ->relationship(
                        name: 'mesin',
                        titleAttribute: 'nama_mesin',
                        modifyQueryUsing: fn($query) =>
                        $query->where('kategori_mesin_id', 1)
                            ->orWhereHas(
                                'kategoriMesin',
                                fn($q) =>
                                $q->where('nama_kategori_mesin', 'ROTARY')
                            )
                    )
                    ->default(2)
                    ->required(),
                DatePicker::make('tgl_produksi')
                    ->label('Tanggal Produksi')
                    ->default(fn() => now()->addDay()) // 👈 default besok
                    ->displayFormat('d F Y') // 👈 tampil seperti: 01 Januari 2025
                    ->required(),
                Textarea::make('kendala')
                    ->columnSpanFull(),
            ]);
    }
}
