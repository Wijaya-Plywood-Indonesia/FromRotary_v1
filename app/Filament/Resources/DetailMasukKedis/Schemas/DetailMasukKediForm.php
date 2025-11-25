<?php

namespace App\Filament\Resources\DetailMasukKedis\Schemas;

use Filament\Schemas\Schema;
use App\Models\JenisKayu;
use App\Models\Ukuran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class DetailMasukKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([

                TextInput::make('no_palet')
                    ->label('Nomor Palet')
                    ->numeric()
                    ->required(),

                Select::make('kode_kedi')
                    ->label('Kode Kedi')
                    ->options([
                        'Kedi 1' => 'Kedi 1',
                        'Kedi 2' => 'Kedi 2',
                        'Kedi 3' => 'Kedi 3',
                        'Kedi 4' => 'Kedi 4',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable(),


                // Relasi ke Jenis Kayu
                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->options(
                        JenisKayu::orderBy('nama_kayu')->pluck('nama_kayu', 'id')
                    )
                    ->searchable()
                    ->afterStateUpdated(function ($state) {
                        session(['last_jenis_kayu' => $state]);
                    })
                    ->default(fn() => session('last_jenis_kayu'))
                    ->required(),

                // Relasi ke Kayu Masuk (Optional)
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->options(
                        Ukuran::all()
                            ->sortBy(fn($u) => $u->dimensi)
                            ->mapWithKeys(fn($u) => [$u->id => $u->dimensi])
                    )
                    ->searchable()
                    ->afterStateUpdated(function ($state) {
                        session(['last_ukuran' => $state]);
                    })
                    ->default(fn() => session('last_ukuran'))
                    ->required(), // Sesuai dengan migrasi

                TextInput::make('kw')
                    ->label('KW (Kualitas)')
                    ->numeric()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Cth: 1, 2, 3,dll.'),

                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->placeholder('Cth: 1.5 atau 100'),

                DatePicker::make('rencana_bongkar')
                    ->label('Rencana Bongkar')
                    ->required(),
            ]);
    }
}
