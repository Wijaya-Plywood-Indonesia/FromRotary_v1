<?php

namespace App\Filament\Resources\DetailMasukKedis\Schemas;

use Filament\Schemas\Schema;

use App\Models\JenisKayu;
use App\Models\Ukuran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class DetailMasukKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

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
                    ->label('KW (Kualitas)')
                    ->numeric()
                    ->required(),


                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required(),


                DatePicker::make('rencana_bongkar')
                    ->label('Rencana Bongkar')
                    ->required(),
            ]);
    }
}
