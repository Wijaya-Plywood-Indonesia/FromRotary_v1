<?php

namespace App\Filament\Resources\ProduksiSandings\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProduksiSandingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->locale('id')                          // Bahasa Indonesia di kalender
                    ->displayFormat('l, j F Y')            // Rabu, 1 Januari 2025
                    ->date(),
                TextEntry::make('kendala')
                    ->placeholder('Belum Ada / Kendala Tidak Di-isi'),
                TextEntry::make('shift'),
            ]);
    }
}
