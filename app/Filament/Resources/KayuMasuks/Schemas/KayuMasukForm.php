<?php

namespace App\Filament\Resources\KayuMasuks\Schemas;

use App\Models\DokumenKayu;
use App\Models\KayuMasuk;
use App\Models\KendaraanSupplierKayu;
use App\Models\SupplierKayu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis_dokumen_angkut')
                    ->label('Jenis Dokumen Angkut')
                    ->options([
                        'SAKR' => 'SAKR',
                        'SK SHHK' => 'SK SHHK',
                        'Nota Angkutan' => 'Nota Angkutan',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                FileUpload::make('upload_dokumen_angkut')
                    ->label('Upload Dokumen Angkut')
                    ->disk('public')
                    ->directory('kayu_masuk/dokumen')
                    ->preserveFilenames()
                    ->required(),

                DatePicker::make('tgl_kayu_masuk')
                    ->label('Tanggal Kayu Masuk')
                    ->default(now()) // otomatis isi dengan waktu sekarang
                    ->readOnly()    // tidak bisa diubah manual
                    ->required(),

                TextInput::make('seri')
                    ->label('Nomor Seri')
                    ->numeric()
                    ->required()
                    ->readOnly()
                    ->default(function () {
                        // Ambil nilai seri terbesar dari database
                        $lastSeri = KayuMasuk::max('seri');

                        // Jika belum ada data, mulai dari 1
                        if (!$lastSeri) {
                            $nextSeri = 1;
                        } else {
                            // Jika terakhir 1000, kembali ke 1
                            $nextSeri = ($lastSeri >= 1000) ? 1 : $lastSeri + 1;
                        }

                        return $nextSeri;
                    })
                    ->hint(function () {
                        // Tampilkan hint di bawah input
                        $lastSeri = KayuMasuk::max('seri');
                        return $lastSeri
                            ? "Seri terakhir di database: {$lastSeri}"
                            : "Belum ada seri sebelumnya (akan dimulai dari 1)";
                    })
                    ->hintColor('info'),

                TextInput::make('kubikasi')
                    ->label('Total Kayu Masuk (m³)')
                    ->default(0)
                    ->numeric()
                    ->disabled() // tidak bisa diubah di form
                    ->dehydrated(true) // tetap dikirim ke database walaupun disabled
                    ->suffix(' m³')
                    ->helperText('Nilai ini akan diperbarui otomatis dari detail kayu masuk.'),

                Select::make('id_supplier_kayus')
                    ->label('Supplier Kayu')
                    ->options(
                        SupplierKayu::query()
                            ->get()
                            ->mapWithKeys(function ($supplier) {
                                return [
                                    $supplier->id => "{$supplier->nama_supplier} - {$supplier->no_telepon}", // sesuaikan kolomnya
                                ];
                            })
                    )
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Supplier Kayu'),

                Select::make('id_kendaraan_supplier_kayus')
                    ->label('Kendaraan Supplier Kayu')
                    ->options(
                        KendaraanSupplierKayu::query()
                            ->get()
                            ->mapWithKeys(function ($kendaraan) {
                                return [
                                    $kendaraan->id => "{$kendaraan->nopol_kendaraan} - {$kendaraan->jenis_kendaraan}", // sesuaikan kolomnya
                                ];
                            })
                    )
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Kendaraan Supplier'),

                Select::make('id_dokumen_kayus')
                    ->label('Dokumen Kayu')
                    ->options(
                        DokumenKayu::query()
                            ->get()
                            ->mapWithKeys(function ($dokumen) {
                                return [
                                    $dokumen->id => "{$dokumen->nama_legal} - {$dokumen->dokumen_legal}", // sesuaikan kolomnya
                                ];
                            })
                    )
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Dokumen Kayu'),
            ]);
    }
}
