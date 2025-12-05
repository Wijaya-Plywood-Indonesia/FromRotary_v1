<?php

namespace App\Filament\Resources\ProduksiSandings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProduksiSandingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('kendala')
                    ->placeholder('Belum Ada / Kendala Tidak Di-isi'),
            ]);
    }
}
