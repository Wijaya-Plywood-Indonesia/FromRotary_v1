<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use App\Models\Pegawai;
use App\Models\KayuMasuk;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Services\WatermarkService;

class DetailTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pegawai')
                    ->label('Pekerja')
                    ->options(
                        Pegawai::query()
                            ->get()
                            ->mapWithKeys(function ($pegawai) {
                                return [
                                    $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                                ];
                            })
                            ->toArray()
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk')
                    ->options(
                        KayuMasuk::query()
                            ->with(['penggunaanSupplier', 'penggunaanKendaraanSupplier'])
                            ->get()
                            ->mapWithKeys(function ($kayu) {
                                $supplier = $kayu->penggunaanSupplier?->nama_supplier ?? '—';
                                $nopol = $kayu->penggunaanKendaraanSupplier?->nopol_kendaraan ?? '—';
                                $jenis = $kayu->penggunaanKendaraanSupplier?->jenis_kendaraan ?? '—';
                                $seri = $kayu->seri ?? '—';

                                return [
                                    $kayu->id => "$supplier | $nopol ($jenis) | Seri: $seri"
                                ];
                            })
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih kayu masuk'),

                // STATUS
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'proses' => 'Sedang Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('menunggu')
                    ->required(),

                // TANDA TANGAN (FOTO)
                FileUpload::make('foto')
                    ->label('Upload bukti')
                    ->disk('public')
                    ->directory('turun-kayu/foto-bukti')
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            // Tambahkan watermark setelah upload
                            $watermarkedPath = WatermarkService::addWatermark($state);
                            $set('foto', $watermarkedPath);
                        }
                    })
                    ->helperText('Upload foto bukti turun kayu. Watermark akan ditambahkan otomatis.')
                    ->columnSpanFull()
                    ->preserveFilenames()
                    ->required(),
            ]);
    }
}