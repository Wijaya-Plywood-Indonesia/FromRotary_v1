<?php

namespace App\Filament\Resources\KayuMasuks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KayuMasukInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('jenis_Dokumen_angkut'),
                TextEntry::make('upload_dokumen_angkut'),
                TextEntry::make('tgl_kayu_masuk')
                    ->dateTime(),
                TextEntry::make('seri')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
