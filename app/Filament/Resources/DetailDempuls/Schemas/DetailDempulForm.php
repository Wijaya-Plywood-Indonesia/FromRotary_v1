<?php

namespace App\Filament\Resources\DetailDempuls\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailDempulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_rencana_pegawai_dempul')
                    ->required()
                    ->numeric(),
                TextInput::make('id_barang_setengah_jadi_hp')
                    ->required()
                    ->numeric(),
                TextInput::make('modal')
                    ->required()
                    ->numeric(),
                TextInput::make('hasil')
                    ->required()
                    ->numeric(),
                TextInput::make('nomor_palet')
                    ->numeric(),
            ]);
    }
}
