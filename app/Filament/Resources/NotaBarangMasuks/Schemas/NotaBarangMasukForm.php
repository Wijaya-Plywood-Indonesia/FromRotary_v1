<?php

namespace App\Filament\Resources\NotaBarangMasuks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotaBarangMasukForm
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
                    ->label('Kepada')
                    ->required(),
                Hidden::make('dibuat_oleh')
                    ->default(fn() => auth()->id())
                    ->dehydrated(fn($context) => $context === 'create'),
                TextInput::make('dibuat_oleh_display')
                    ->label('Dibuat Oleh')
                    ->formatStateUsing(
                        fn($record) =>
                        $record?->dibuatOleh?->name ?? auth()->user()->name
                    )
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
