<?php

namespace App\Filament\Resources\NotaBarangKeluars\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotaBarangKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->default(today())
                    ->required(),
                TextInput::make('no_nota')
                    ->unique()
                    ->required(),
                TextInput::make('tujuan_nota')
                    ->required(),
                Hidden::make('dibuat_oleh')
                    ->default(fn() => auth()->user()?->id),

                TextInput::make('dibuat_oleh_display')
                    ->label('Dibuat Oleh')
                    ->default(fn() => auth()->user()?->name)
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
