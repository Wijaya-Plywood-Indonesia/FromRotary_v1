<?php

namespace App\Filament\Resources\NotaKayus\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NotaKayuInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_kayu_masuk')
                    ->numeric(),
                TextEntry::make('no_nota'),
                TextEntry::make('penanggung_jawab'),
                TextEntry::make('penerima'),
                TextEntry::make('satpam'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
