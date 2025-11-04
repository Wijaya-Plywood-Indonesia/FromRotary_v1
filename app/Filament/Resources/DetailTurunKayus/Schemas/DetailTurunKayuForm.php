<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use App\Models\Pegawai;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->options(
                        Pegawai::query()
                            ->get()
                            ->mapWithKeys(fn($pegawai) => [
                                $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                            ])
                    )
                    //   ->multiple() // bisa pilih banyak
                    ->searchable()
                    ->required(),
            ]);
    }

}
