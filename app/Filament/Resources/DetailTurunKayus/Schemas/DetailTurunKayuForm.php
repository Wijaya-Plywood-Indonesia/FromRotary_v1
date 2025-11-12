<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use App\Models\Pegawai;
use App\Models\KayuMasuk;
use App\Models\KendaraanSupplierKayu; // <-- Tambahkan Import
use App\Models\SupplierKayu; // <-- Tambahkan Import
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get; // <-- Tambahkan Import
use Filament\Schemas\Schema;

class DetailTurunKayuForm
{
    /**
     * Konfigurasi schema form untuk DetailTurunKayu.
     * Didesain untuk bekerja dengan Relation Manager
     * yang membuat banyak record sekaligus.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([


                // Pilih BANYAK Pegawai (Tidak berubah)
                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->options(
                        Pegawai::query()
                            // Anda bisa filter misal: ->where('status', 'aktif')
                            ->get()
                            ->mapWithKeys(fn($pegawai) => [
                                $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                            ])
                    )
                    ->searchable()
                    ->multiple() // <-- PENTING: Izinkan memilih banyak pegawai
                    ->preload()
                    ->required(),

                // 1. Pilih Supplier
                Select::make('id_supplier_kayus') // Key sementara untuk filter
                    ->label('Supplier Kayu')
                    ->options(SupplierKayu::query()->pluck('nama_supplier', 'id'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required(), // Wajib diisi untuk memfilter

                // 2. Pilih Kendaraan (terfilter oleh Supplier)
                Select::make('id_kendaraan_supplier_kayus') // Key sementara untuk filter
                    ->label('Kendaraan (Nopol & Jenis)')
                    ->options(function (\Filament\Schemas\Components\Utilities\Get $get) { // <-- PERBAIKAN: Tambahkan \ di sini
                        $supplierId = $get('id_supplier_kayus');
                        if (!$supplierId) {
                            return []; // Kosong jika supplier belum dipilih
                        }

                        // Asumsi: Model KendaraanSupplierKayu PUNYA 'id_supplier_kayus'
                        return KendaraanSupplierKayu::query()
                            ->where('id_supplier', $supplierId)
                            ->get()
                            ->mapWithKeys(fn($kendaraan) => [
                                $kendaraan->id => "{$kendaraan->nopol_kendaraan} ({$kendaraan->jenis_kendaraan})"
                            ])
                            ->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->required(), // Wajib diisi untuk memfilter

                // 3. Pilih Kayu Masuk (Seri) (terfilter oleh Supplier & Kendaraan)
                Select::make('id_kayu_masuk') // <-- INI FIELD YANG AKAN DISIMPAN
                    ->label('Kayu Masuk (Seri)')
                    ->options(function (\Filament\Schemas\Components\Utilities\Get $get) { // <-- PERBAIKAN: Tambahkan \ di sini
                        $supplierId = $get('id_supplier_kayus');
                        $kendaraanId = $get('id_kendaraan_supplier_kayus');

                        if (!$supplierId || !$kendaraanId) {
                            return []; // Kosong jika filter atas belum lengkap
                        }

                        // Query KayuMasuk berdasarkan 2 filter di atas
                        return KayuMasuk::query()
                            ->where('id_supplier', $supplierId)
                            ->where('id_kendaraan_supplier_kayus', $kendaraanId)
                            ->get()
                            ->mapWithKeys(fn($kayuMasuk) => [
                                $kayuMasuk->id => "Seri: {$kayuMasuk->seri}"
                            ])
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),

                // --- AKHIR PENGGANTI DROPDOWN ---


            ]);
    }
}