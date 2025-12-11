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
                    ->label('Kepada')
                    ->required(),
               Hidden::make('dibuat_oleh')
    ->default(fn() => auth()->id()),

TextInput::make('dibuat_oleh_display')
    ->label('Dibuat Oleh')
    ->disabled()
    ->dehydrated(false) // jangan disimpan, hanya tampilan
    ->afterStateHydrated(function ($component, $state, $record) {
        // Kalau sedang edit → ambil nama pembuat dari relasi
        if ($record) {
            $component->state(
                $record->pembuat?->name ?? '-'
            );
        } else {
            // Kalau create → ambil user login
            $component->state(auth()->user()->name);
        }
    }),
            ]);
    }
}
