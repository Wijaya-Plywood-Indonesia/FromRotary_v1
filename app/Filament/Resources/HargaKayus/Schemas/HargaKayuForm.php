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
                Select::make('panjang')
                    ->label('Panjang')
                    ->options([
                        130 => '130',
                        260 => '260',
                    ])
                    ->required()
                    ->default(260)
                    ->native(false)
                    ->searchable()
                    ->preload(),
                TextInput::make('diameter_terkecil')
                    ->label('Diameter Terkecil (cm)')
                    ->numeric(),
                TextInput::make('diameter_terbesar')
                    ->label('Diameter Terkecil (cm)')
                    ->numeric(),
                TextInput::make('harga_beli')
                    ->label('Harga Beli Per Batang')
                    ->required()
                    //    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->numeric(),

                Select::make('grade')
                    ->label('Grade')
                    ->options([
                        1 => 'Grade A',
                        2 => 'Grade B',
                    ])
                    ->required()
                    ->default(2)
                    ->native(false)
                    ->searchable(),

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
                    ->default(1)
                    ->required(),
            ]);
    }
}
