<?php

namespace App\Filament\Resources\DetailTurunKayus\Schemas;

use App\Models\Pegawai;
use App\Models\KayuMasuk;
use App\Models\DetailTurunKayu;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Log;
use App\Services\WatermarkService;
use Illuminate\Database\Eloquent\Builder; // Tambahan untuk query builder

class DetailTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk')
                    // Tambahkan parameter $livewire untuk mengakses data Parent (TurunKayu)
                    ->options(function (callable $get, $livewire) {
                        
                        $currentId = $get('id_kayu_masuk');
                        
                        // 1. AMBIL TANGGAL TRANSAKSI SAAT INI (PARENT)
                        // Karena ini di RelationManager, kita akses ownerRecord
                        $parentRecord = $livewire->ownerRecord ?? null;
                        
                        // Default ke hari ini jika tidak ada parent (jaga-jaga)
                        // GANTI 'tanggal' dengan nama kolom tanggal di tabel turun_kayus Anda (misal: tanggal_turun)
                        $tanggalTurun = $parentRecord?->tanggal ?? now(); 

                        // 2. QUERY LANGSUNG (LEBIH RINGAN & TEPAT)
                        return KayuMasuk::query()
                            ->with(['penggunaanSupplier', 'penggunaanKendaraanSupplier'])
                            
                            // A. Filter Tanggal: Hanya tampilkan kayu yang masuk SEBELUM atau SAMA DENGAN tanggal turun
                            ->whereDate('tgl_kayu_masuk', '<=', $tanggalTurun)
                            
                            // B. Filter Ketersediaan: 
                            // Tampilkan yang BELUM dipakai di detail_turun_kayu MANAPUN
                            // KECUALI jika itu adalah kayu yang sedang kita edit saat ini ($currentId)
                            ->where(function (Builder $query) use ($currentId) {
                                $query->whereDoesntHave('detailTurunKayus');
                                
                                if ($currentId) {
                                    $query->orWhere('id', $currentId);
                                }
                            })
                            
                            ->orderByDesc('seri')
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
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    // Validasi unik agar user tidak bisa memaksa input ganda
                    ->unique(table: 'detail_turun_kayus', column: 'id_kayu_masuk', ignoreRecord: true),

                // ... (SISA KODE STATUS DAN LAINNYA TETAP SAMA SEPERTI ASLINYA) ...
                
                // STATUS
                Select::make('status')
                    ->label('Status')
                    ->options(function (callable $get) {
                        $kayuMasukId = $get('id_kayu_masuk');

                        if (!$kayuMasukId) {
                            return [
                                'menunggu' => 'Menunggu',
                                'selesai' => 'Selesai',
                            ];
                        }

                        $kayuMasuk = KayuMasuk::with('penggunaanKendaraanSupplier')
                            ->find($kayuMasukId);

                        $jenis = $kayuMasuk?->penggunaanKendaraanSupplier?->jenis_kendaraan;

                        if ($jenis === 'Fuso') {
                            return [
                                'menunggu' => 'Menunggu',
                                'selesai' => 'Selesai',
                            ];
                        }

                        return [
                            'selesai' => 'Selesai',
                        ];
                    })
                    ->reactive()
                    ->native(false)
                    ->required(),

                TextInput::make('nama_supir')
                    ->label('Nama Supir')
                    ->required(),

                TextInput::make('jumlah_kayu')
                    ->label('Jumlah Kayu')
                    ->required()
                    ->numeric(),

                FileUpload::make('foto')
                    ->label('Foto Bukti')
                    ->disk('public')
                    ->directory('turun-kayu/foto-bukti')
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->required(),
            ]);
    }
}