<?php

namespace App\Filament\Resources\UkuranBarangSetengahJadis\Schemas;
use App\Models\Ukuran;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Symfony\Contracts\Service\Attribute\Required;

class UkuranBarangSetengahJadiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->options(
                        Ukuran::all()
                            ->pluck('dimensi', 'id'))
            ->searchable()
            ->Required(),

            TextInput::make('grade')
                    ->label('Grade')
                    ->required()
                    ->maxLength(255),
                    // ->placeholder('Cth: 1, 2, 3,dll.'),

            TextInput::make('keterangan')
                    ->label('Keterangan')
            ]);
    }
}
