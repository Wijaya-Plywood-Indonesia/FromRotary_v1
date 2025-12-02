<?php

namespace App\Filament\Resources\PlatformHasilHps\Schemas;

use Filament\Schemas\Schema;
use App\Models\JenisKayu;
use App\Models\Mesin;
use App\Models\BarangSetengahJadiHp;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PlatformHasilHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_mesin')
                    ->label('Mesin Hotpress')
                    ->options(
                        Mesin::whereHas('kategoriMesin', function ($query) {
                            $query->where('nama_kategori_mesin', 'HOTPRESS');
                        })
                            ->orderBy('nama_mesin')
                            ->pluck('nama_mesin', 'id')
                    )
                    ->searchable()
                    ->required(),

                TextInput::make('no_palet')
                    ->label('Nomor Palet')
                    ->numeric()
                    ->required(),

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

                Select::make('id_ukuran_setengah_jadi')
                    ->label('Ukuran')
                    ->options(
                        BarangSetengahJadiHp::orderBy('grade')
                            ->pluck('grade', 'id')
                    )
                    ->searchable()
                    ->afterStateUpdated(function ($state) {
                        session(['last_ukuran_setengah_jadi' => $state]);
                    })
                    ->default(fn() => session('last_ukuran_setengah_jadi'))
                    ->required(),

                TextInput::make('kw')
                    ->label('KW (Kualitas)')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Cth: 1, 2, 3,dll.'),

                TextInput::make('isi')
                    ->label('Isi')
                    ->required()
                    ->numeric()
                    ->placeholder('Cth: 1.5 atau 100'),
            ]);
    }
}
