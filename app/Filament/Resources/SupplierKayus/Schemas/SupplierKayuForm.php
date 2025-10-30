<?php

namespace App\Filament\Resources\SupplierKayus\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SupplierKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_supplier')
                    ->required(),
                TextInput::make('no_telepon')
                    ->tel()
                    ->required(),
                TextInput::make('nik')

                    ->label('Nomor Induk Kependudukan')
                    ->minLength(16)
                    ->maxLength(16),

                Select::make('jenis_kelamin_pegawai')
                    ->label('Jenis Kelamin')
                    ->options([
                        0 => 'Perempuan',
                        1 => 'Laki-laki',
                    ])

                    ->default('0')
                    ->native(false),

                Textarea::make('alamat')
                    ->columnSpanFull(),
                Select::make('jenis_bank')
                    ->label('Jenis Bank')
                    ->options([
                        'BCA' => 'BCA (Bank Central Asia)',
                        'BRI' => 'BRI (Bank Rakyat Indonesia)',
                        'BNI' => 'BNI (Bank Negara Indonesia)',
                        'Mandiri' => 'Mandiri',
                        'BSI' => 'BSI (Bank Syariah Indonesia)',
                        'CIMB' => 'CIMB Niaga',
                        'BTN' => 'BTN (Bank Tabungan Negara)',
                        'Lainnya' => 'Lainnya (Ketik manual)',
                    ])
                    ->searchable()
                    ->live() // supaya bisa reaktif

                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state !== 'Lainnya') {
                            $set('bank_lainnya', null); // reset input custom kalau pilih bank umum
                        }
                    }),
                TextInput::make('no_rekening'),

                Toggle::make('status_supplier')
                    ->label('Status Supplier')
                    ->reactive()            // bikin field reaktif sehingga placeholder update segera
                    ->default(true)
                    ->required(),


                // TextEntry::make('status_supplier_label')
                //     ->label('Status')
                //     ->disabled()
                //     ->default(fn($get) => $get('status_supplier') ? 'Aktif' : 'Tidak Aktif'),

            ]);
    }
}
