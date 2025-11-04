<?php

namespace App\Filament\Resources\HargaKayus\Schemas;

use App\Models\JenisKayu;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HargaKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('panjang')
                    ->required()
                    ->numeric()
                    ->placeholder('120 / 260')
                ,
                TextInput::make('diameter_terkecil')
                    ->label('Diameter Terkecil (cm)')
                    ->numeric(),
                TextInput::make('diameter_terbesar')
                    ->label('Diameter Terbesar (cm)')
                    ->numeric(),
                TextInput::make('harga_beli')
                    ->label('Harga Beli Per Batang')
                    ->required()
                    //    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->numeric(),
                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->options(
                        JenisKayu::query()
                            ->get()
                            ->mapWithKeys(function ($JenisKayu) {
                                return [
                                    $JenisKayu->id => "{$JenisKayu->kode_kayu} - {$JenisKayu->nama_kayu}",
                                ];
                            })
                    )
                    ->searchable()
                    ->required(),
            ]);
    }
}
