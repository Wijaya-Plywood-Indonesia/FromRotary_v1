<?php

namespace App\Filament\Resources\Repairs\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class RepairInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->label('Tanggal')
                    ->date('d F Y'),
                TextEntry::make('jumlah_meja')
                    ->label('Jumlah Meja')
                    ->numeric(),
            ]);
    }
}
