<?php

namespace App\Filament\Resources\RencanaRepairs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RencanaRepairInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
