<?php

namespace App\Filament\Resources\DetailMasukKedis\Schemas;

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
                TextInput::make('id_jenis_kayu')
                    ->numeric(),
                TextInput::make('kw')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                DatePicker::make('rencana_bongkar')
                    ->required(),
                TextInput::make('id_produksi_kedi')
                    ->required()
                    ->numeric(),
            ]);
    }
}
