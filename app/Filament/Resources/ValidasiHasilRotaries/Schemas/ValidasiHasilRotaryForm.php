<?php

namespace App\Filament\Resources\ValidasiHasilRotaries\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ValidasiHasilRotaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Select::make('role')
                //     ->label('Role')
                //     ->options([
                //         'pengawas_produksi_1' => 'Pengawas Produksi 1',
                //         'pengawas_produksi_2' => 'Pengawas Produksi 2',
                //         'kepala_produksi' => 'Kepala Produksi',
                //         'pimpinan' => 'Pimpinan',
                //     ])
                //     ->required()
                //     ->native(false)
                //     ->searchable(),

                TextInput::make('role')
                    ->label('Role Login')
                    ->default(function () {
                        $user = Filament::auth()->user();

                        if (!$user) {
                            return 'Tidak diketahui';
                        }

                        // Ambil role pertama dari user (karena bisa punya lebih dari satu)
                        /** @var User&HasRoles $user */
                        return $user->getRoleNames()->first() ?? 'Tidak diketahui';
                    })
                    ->disabled()
                    ->dehydrated(true), // tetap ikut disimpan ke database
                Select::make('status')
                    ->label('Status Validasi')
                    ->options([
                        'divalidasi' => 'Divalidasi',
                        'disetujui' => 'Disetujui',
                        'ditangguhkan' => 'Ditangguhkan',
                        'ditolak' => 'Ditolak',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable(),
            ]);
    }
}
