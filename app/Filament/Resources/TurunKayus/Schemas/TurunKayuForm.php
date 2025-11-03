<?php

namespace App\Filament\Resources\TurunKayus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class TurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')

            ]);
    }
}
