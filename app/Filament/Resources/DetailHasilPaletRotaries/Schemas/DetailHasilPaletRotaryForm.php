<?php

namespace App\Filament\Resources\DetailHasilPaletRotaries\Schemas;

use App\Models\PenggunaanLahanRotary;
use App\Models\Ukuran;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

class DetailHasilPaletRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                DateTimePicker::make('timestamp_laporan')
                    ->default(now())
                    ->required(),
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->options(
                        Ukuran::all()
                            ->pluck('dimensi', 'id') // ← memanggil accessor getDimensiAttribute()
                    )
                    ->searchable()
                    ->required(),

                TextInput::make('kw')
                    ->required(),

                Select::make('id_penggunaan_lahan')
                    ->label('Kode Lahan')
                    ->options(function (RelationManager $livewire) {
                        $parent = $livewire->getOwnerRecord(); // ← ambil parent record (ProduksiRotary)
                        $idProduksi = $parent->id; // gunakan id produksinya
            
                        return PenggunaanLahanRotary::with('lahan')
                            ->where('id_produksi', $idProduksi)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->lahan->kode_lahan ?? 'Tanpa Kode'];
                            });
                    })
                    ->searchable()
                    ->required(),

                TextInput::make('palet')
                    ->required(),
                TextInput::make('total_lembar')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
