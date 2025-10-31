<?php

namespace App\Filament\Resources\DokumenKayus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DokumenKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Dokumen Sumber Kayu')
                    ->description('Lengkapi dokumen legalitas dan identitas sumber kayu.')
                    ->schema([

                        TextInput::make('nama_legal')
                            ->label('Nama Sesuai KTP dan Dokumen Legal')
                            ->required(),

                        Select::make('dokumen_legal')
                            ->label('Jenis Dokumen Legal')
                            ->options([
                                'SHM' => 'Sertifikat Hak Milik (SHM)',
                                'Letter C' => 'Letter C',
                            ])
                            ->required()
                            ->native(false),

                        FileUpload::make('upload_dokumen')
                            ->label('Upload Dokumen Legal')
                            ->disk('public')
                            ->directory('sumber-kayu/dokumen')
                            ->preserveFilenames()
                            ->required(),

                        FileUpload::make('upload_ktp')
                            ->label('Upload KTP Pemilik')
                            ->disk('public')
                            ->directory('sumber-kayu/ktp')
                            ->preserveFilenames()
                            ->required(),

                        FileUpload::make('foto_lokasi')
                            ->label('Foto Lokasi')
                            ->disk('public')
                            ->directory('sumber-kayu/foto-lokasi')
                            ->preserveFilenames()
                            ->image(),
                    ]),

                /** =========================
                 * 📍 BAGIAN DATA LOKASI
                 * ========================= */
                Section::make('Informasi Lokasi Sumber Kayu')
                    ->description('Isi alamat lengkap dan tandai lokasi di peta.')
                    ->schema([
                        TextInput::make('nama_tempat')
                            ->label('Nama Tempat / Area')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('alamat_lengkap')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3),

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->reactive()
                            ->required(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->reactive()
                            ->required(),
                    ]),

                /** =========================
                 * 🗺️ BAGIAN GOOGLE MAPS
                 * ========================= */
                // Section::make('Tandai Lokasi di Peta')
                //     ->schema([
                //         ViewField::make('map')
                //             ->view('filament.forms.components.google-map-picker')
                //             ->label(false),
                //     ]),
            ]);
    }
}
