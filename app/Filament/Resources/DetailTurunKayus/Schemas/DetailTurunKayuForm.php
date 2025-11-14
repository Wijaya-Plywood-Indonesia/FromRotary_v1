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
                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->relationship(
                        name: 'pegawai',
                        titleAttribute: 'nama_pegawai'
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record ? "{$record->kode_pegawai} - {$record->nama_pegawai}" : '—'
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih satu atau lebih pegawai'),

                // 2. KAYU MASUK (SINGLE) → SUPPLIER + KENDARAAN + SERI
                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk (Supplier | Kendaraan | Seri)')
                    ->relationship(
                        name: 'kayuMasuk',
                        titleAttribute: 'seri'
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($kayu) =>
                        $kayu
                        ? ($kayu->penggunaanSupplier?->nama_supplier ?? '—') . ' | ' .
                        ($kayu->penggunaanKendaraanSupplier?->nopol_kendaraan ?? '—') . ' (' .
                        ($kayu->penggunaanKendaraanSupplier?->jenis_kendaraan ?? '—') . ') | ' .
                        "Seri: {$kayu->seri}"
                        : '— Kayu Masuk Tidak Ditemukan —'
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih kayu masuk'),

            ]);
    }
}