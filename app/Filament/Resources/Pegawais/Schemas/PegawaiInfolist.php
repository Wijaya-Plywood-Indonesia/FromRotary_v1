<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PegawaiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_pegawai'),
                TextEntry::make('nama_pegawai'),
                TextEntry::make('no_telepon_pegawai'),
                IconEntry::make('jenis_kelamin_pegawai')
                    ->boolean(),
                TextEntry::make('tanggal_masuk')
                    ->date(),
                TextEntry::make('foto'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
