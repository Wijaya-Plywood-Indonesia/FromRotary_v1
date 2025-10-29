<?php

namespace App\Filament\Resources\ProduksiRotaries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProduksiRotaryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mesin.nama_mesin')
                    ->numeric(),
                TextEntry::make('tgl_produksi')
                    ->date(),
                TextEntry::make('kendala'),

            ]);
    }
}
