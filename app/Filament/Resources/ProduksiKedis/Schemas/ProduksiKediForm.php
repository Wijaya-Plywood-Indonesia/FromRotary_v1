<?php

namespace App\Filament\Resources\ProduksiKedis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProduksiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->required(),
                Textarea::make('kendala')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['bongkar' => 'Bongkar', 'masuk' => 'Masuk'])
                    ->required(),
            ]);
    }
}
