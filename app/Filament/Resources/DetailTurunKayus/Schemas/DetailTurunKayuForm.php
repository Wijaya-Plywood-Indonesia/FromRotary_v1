<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use App\Models\Pegawai;
use App\Models\KayuMasuk;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Log;
use App\Services\WatermarkService;

class DetailTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

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


                TextInput::make('nama_supir')
                    ->label('Nama Supir')
                    ->required(),

                // TANDA TANGAN (FOTO)
                FileUpload::make('foto')
                    ->label('Foto Bukti')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('turun-kayu/foto-bukti')
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()

                    ->required(),
            ]);
    }
}