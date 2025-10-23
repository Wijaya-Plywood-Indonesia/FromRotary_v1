<?php

namespace App\Filament\Resources\PenggunaanLahanRotaries\Schemas;

use App\Models\Lahan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenggunaanLahanRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_lahan')
                    ->label('Lahan')
                    ->options(
                        Lahan::query()
                            ->get()
                            ->mapWithKeys(function ($lahan) {
                                return [
                                    $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                                ];
                            })
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('jumlah_batang')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
