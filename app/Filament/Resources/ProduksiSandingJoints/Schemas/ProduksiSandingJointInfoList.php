<?php

namespace App\Filament\Resources\ProduksiSandingJoints\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProduksiSandingJointInfoList
{
    public static function configure(Schema $schema): schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal_produksi')
                ->date(),
                TextEntry::make('kendala'),
            ]);
    }
}
