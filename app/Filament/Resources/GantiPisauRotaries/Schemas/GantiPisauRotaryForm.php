<?php

namespace App\Filament\Resources\GantiPisauRotaries\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GantiPisauRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis_kendala')
                    ->label('Jenis Kendala')
                    ->options([
                        'Ganti Pisau' => 'Ganti Pisau',
                        'Lain-lain' => 'Lain-lain',
                    ])
                    ->native(false)
                    ->required()
                    ->live(), // Wajib live agar form bisa bereaksi

                // Field ini hanya muncul jika user memilih 'Lain-lain'
                TextInput::make('keterangan') 
                    ->label('Tambahkan Keterangan')
                    ->placeholder('Deskripsikan kendala...')
                    // Removed 'Get' type hint to fix TypeError
                    ->visible(fn ($get) => $get('jenis_kendala') === 'Lain-lain')
                    ->required(), // Wajib diisi jika muncul

                // Waktu Mulai (Otomatis)
                Hidden::make('jam_mulai_ganti_pisau')
                    ->default(now()->format('H:i')),
            ]);
    }
}