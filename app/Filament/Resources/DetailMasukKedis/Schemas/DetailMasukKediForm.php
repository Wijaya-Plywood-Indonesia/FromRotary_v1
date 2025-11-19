<?php

namespace App\Filament\Resources\DetailMasukKedis\Schemas;

use App\Models\JenisKayu;
use App\Models\Ukuran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailMasukKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_palet')
                    ->label('No Palet Basah')
                    ->required()
                    ->numeric(),

                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->options(
                        Ukuran::all()
                            ->pluck('dimensi', 'id') // ← memanggil accessor getDimensiAttribute()
                    )
                    ->searchable()
                    ->required(),

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

                TextInput::make('kw')
                    ->required()
                    ->numeric(),

                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),

                DatePicker::make('rencana_bongkar')
                    ->required(),
            ]);
    }
}
