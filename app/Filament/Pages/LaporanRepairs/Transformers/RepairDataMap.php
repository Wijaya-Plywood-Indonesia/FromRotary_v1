<?php

namespace App\Filament\Pages\LaporanRepairs\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class RepairDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $produksi) {
            $tanggal = Carbon::parse($produksi->tanggal)->format('d/m/Y');

            foreach ($produksi->modalRepairs as $modal) {
                $ukuran = $modal->ukuran->nama_ukuran ?? 'TIDAK ADA UKURAN';
                $jenisKayu = $modal->jenisKayu->nama_kayu ?? 'TIDAK ADA JENIS';
                $kw = $modal->kw ?? $modal->kualitas ?? 1;

                // =====================================================
                // PERBAIKAN: Pencarian Target yang Lebih Akurat
                // =====================================================

                // 1. Cari target dengan matching exact KW
                $targetModel = Target::where('id_mesin', $produksi->id_mesin)
                    ->where('id_ukuran', $modal->id_ukuran)
                    ->where('id_jenis_kayu', $modal->id_jenis_kayu)
                    ->where('kode_ukuran', 'LIKE', 'REPAIR%' . $kw . 's')
                    ->first();

                // 2. Fallback: Cari target dengan KW apapun (ambil yang pertama)
                if (!$targetModel) {
                    $targetModel = Target::where('id_mesin', $produksi->id_mesin)
                        ->where('id_ukuran', $modal->id_ukuran)
                        ->where('id_jenis_kayu', $modal->id_jenis_kayu)
                        ->where('kode_ukuran', 'LIKE', 'REPAIR%')
                        ->first();
                }

                // 3. Fallback terakhir: Cari berdasarkan mesin dan ukuran saja
                if (!$targetModel) {
                    $targetModel = Target::where('id_mesin', $produksi->id_mesin)
                        ->where('id_ukuran', $modal->id_ukuran)
                        ->where('kode_ukuran', 'LIKE', 'REPAIR%')
                        ->first();
                }

                // Jika masih tidak ketemu, log warning
                if (!$targetModel) {
                    Log::warning("Target tidak ditemukan untuk:", [
                        'mesin' => $produksi->id_mesin,
                        'ukuran' => $modal->id_ukuran,
                        'jenis_kayu' => $modal->id_jenis_kayu,
                        'kw' => $kw
                    ]);
                }

                // Ambil data target
                $kodeUkuran = $targetModel?->kode_ukuran ?? "REPAIR-UNKNOWN-KW{$kw}";
                $targetHarian = $targetModel?->target ?? ($modal->target ?? 0);
                $jamProduksi = $targetModel?->jam ?? ($modal->jam_kerja ?? 10);
                $potonganPerLembar = $targetModel?->potongan ?? 0;
                $jumlahOrangTarget = $targetModel?->orang ?? 1;

                // Hitung total hasil dari semua pekerja untuk modal ini
                $totalHasil = $modal->rencanaRepairs
                    ->flatMap->hasilRepairs
                    ->sum('jumlah');

                $selisih = $totalHasil - $targetHarian;

                // =====================================================
                // HITUNG POTONGAN PER ORANG
                // =====================================================
                $jumlahPekerja = $produksi->rencanaPegawais
                    ->filter(fn($rp) => $rp->pegawai)
                    ->count();

                $potonganPerOrang = 0;

                if ($targetHarian > 0 && $selisih < 0 && $potonganPerLembar > 0 && $jumlahPekerja > 0) {
                    $totalDenda = abs($selisih) * $potonganPerLembar;
                    $potonganPerOrangRaw = $totalDenda / $jumlahPekerja;

                    // Pembulatan ke 500an terdekat
                    $ribuan = floor($potonganPerOrangRaw / 1000);
                    $ratusan = $potonganPerOrangRaw % 1000;

                    if ($ratusan < 300) {
                        $potonganPerOrang = $ribuan * 1000;
                    } elseif ($ratusan >= 300 && $ratusan < 800) {
                        $potonganPerOrang = ($ribuan * 1000) + 500;
                    } else {
                        $potonganPerOrang = ($ribuan + 1) * 1000;
                    }
                }

                // =====================================================
                // MAPPING PEKERJA
                // =====================================================
                $pekerja = [];
                $nomorMeja = '-';

                foreach ($produksi->rencanaPegawais as $rp) {
                    $pegawai = $rp->pegawai;

                    if ($pegawai) {
                        // Ambil nomor meja (gunakan yang pertama ditemukan)
                        if (!empty($rp->nomor_meja) && $nomorMeja === '-') {
                            $nomorMeja = $rp->nomor_meja;
                        }

                        // Format jam
                        $jamMasuk = $rp->jam_masuk
                            ? Carbon::parse($rp->jam_masuk)->format('H:i')
                            : '-';
                        $jamPulang = $rp->jam_pulang
                            ? Carbon::parse($rp->jam_pulang)->format('H:i')
                            : '-';

                        // Hitung hasil individu pekerja untuk modal ini
                        $hasilIndividu = $rp->rencanaRepairs
                            ->where('id_modal_repair', $modal->id)
                            ->flatMap->hasilRepairs
                            ->sum('jumlah');

                        $pekerja[] = [
                            'id' => $pegawai->kode_pegawai ?? '-',
                            'nama' => $pegawai->nama_pegawai ?? '-',
                            'jam_masuk' => $jamMasuk,
                            'jam_pulang' => $jamPulang,
                            'ijin' => $rp->ijin ?? '-',
                            'keterangan' => $rp->keterangan ?? '-',
                            'nomor_meja' => $rp->nomor_meja ?? '-',
                            'pot_target' => $potonganPerOrang,
                            'hasil' => $hasilIndividu,
                        ];
                    }
                }

                // =====================================================
                // SUSUN DATA UNTUK SETIAP KOMBINASI
                // =====================================================
                $result[] = [
                    'nomor_meja' => $nomorMeja,
                    'ukuran' => $ukuran,
                    'jenis_kayu' => $jenisKayu,
                    'kode_ukuran' => $kodeUkuran, // Kunci untuk grouping
                    'kw' => $kw,
                    'pekerja' => $pekerja,
                    'hasil' => $totalHasil,
                    'target' => $targetHarian,
                    'jam_kerja' => $jamProduksi,
                    'jumlah_orang_target' => $jumlahOrangTarget,
                    'selisih' => $selisih,
                    'tanggal' => $tanggal,
                    'potongan_per_lembar' => $potonganPerLembar,
                ];
            }
        }

        return $result;
    }
}