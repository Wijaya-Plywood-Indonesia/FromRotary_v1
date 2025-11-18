<?php

namespace App\Filament\Resources\PegawaiRepairs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PegawaiRepairInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_pegawai')
                    ->numeric(),
                TextEntry::make('jam_masuk')
                    ->time(),
                TextEntry::make('jam_pulang')
                    ->time(),
                TextEntry::make('izin'),
                TextEntry::make('keterangan'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
