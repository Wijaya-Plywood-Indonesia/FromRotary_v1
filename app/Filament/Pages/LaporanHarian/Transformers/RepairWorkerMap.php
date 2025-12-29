<?php

namespace App\Filament\Pages\LaporanHarian\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class RepairWorkerMap
{
    public static function make($collection): array
    {
        $results = [];

        foreach ($collection as $produksi) {

            // 1. Loop Modal (Ukuran Kayu yang dikerjakan)
            foreach ($produksi->modalRepairs as $modal) {

                // --- A. KONSTRUKSI LABEL & UKURAN ---
                $ukuranModel = $modal->ukuran;
                $jenisKayuModel = $modal->jenisKayu;
                $kw = $modal->kw ?? $modal->kualitas ?? 1;

                // Label Visual
                $labelPekerjaan = 'REPAIR';
                if ($ukuranModel) {
                    $labelPekerjaan .= ' ' . $ukuranModel->panjang . 'x' . $ukuranModel->lebar;
                }

                // --- LOGGING STEP 1: DATA MENTAH ---
                // Kita cek data apa yang masuk dari Modal Repair
                Log::info("🔍 [REPAIR DEBUG] Memproses Modal ID: {$modal->id}", [
                    'Panjang' => $ukuranModel->panjang ?? 'NULL',
                    'Lebar' => $ukuranModel->lebar ?? 'NULL',
                    'Tebal' => $ukuranModel->tebal ?? 'NULL',
                    'KW' => $kw,
                    'Jenis' => $jenisKayuModel->kode_kayu ?? 'NULL',
                    'ID Mesin' => $produksi->id_mesin
                ]);

                // Buat Kode Ukuran
                $kodeUkuran = 'REPAIR-NOT-FOUND';

                if ($ukuranModel && $jenisKayuModel) {
                    // FORMAT: REPAIR + P + L + T(koma) + KW + JENIS(Upper)
                    $kodeUkuran = 'REPAIR' .
                        $ukuranModel->panjang .
                        $ukuranModel->lebar .
                        str_replace('.', ',', $ukuranModel->tebal) .
                        $kw .
                        strtoupper($jenisKayuModel->kode_kayu); // Pastikan Uppercase
                }

                // --- LOGGING STEP 2: STRING YANG DIHASILKAN ---
                Log::info("🧩 [REPAIR DEBUG] String Pencarian: '{$kodeUkuran}'");

                // --- B. CARI TARGET ---
                $targetModel = null;
                $levelFound = 0;

                // Level 1: String Persis + Mesin Spesifik
                $targetLv1 = Target::where('kode_ukuran', $kodeUkuran)
                    ->where('id_mesin', $produksi->id_mesin)
                    ->first();

                if ($targetLv1) {
                    $targetModel = $targetLv1;
                    $levelFound = 1;
                } else {
                    // Level 2: String Persis (Mesin Umum)
                    $targetLv2 = Target::where('kode_ukuran', $kodeUkuran)->first();
                    if ($targetLv2) {
                        $targetModel = $targetLv2;
                        $levelFound = 2;
                    } else {
                        // Level 3: Fallback ID
                        $targetLv3 = Target::where([
                            'id_mesin' => $produksi->id_mesin,
                            'id_ukuran' => $modal->id_ukuran,
                            'id_jenis_kayu' => $modal->id_jenis_kayu,
                        ])->first();

                        if ($targetLv3) {
                            $targetModel = $targetLv3;
                            $levelFound = 3;
                        }
                    }
                }

                // --- LOGGING STEP 3: HASIL PENCARIAN TARGET ---
                if ($targetModel) {
                    Log::info("✅ [REPAIR DEBUG] Target DITEMUKAN di Level {$levelFound}", [
                        'Target' => $targetModel->target,
                        'Potongan' => $targetModel->potongan
                    ]);
                } else {
                    Log::warning("❌ [REPAIR DEBUG] Target TIDAK DITEMUKAN untuk '{$kodeUkuran}'");
                }

                $targetWajib = (int) ($targetModel->target ?? 0);
                $potonganPerLembar = (int) ($targetModel->potongan ?? 0);

                // --- C. LOOP PEGAWAI ---
                foreach ($produksi->rencanaPegawais as $rp) {

                    if (!$rp->pegawai)
                        continue;

                    // Hitung Hasil
                    $hasilIndividu = 0;
                    if ($rp->rencanaRepairs) {
                        $hasilIndividu = $rp->rencanaRepairs
                            ->where('id_modal_repair', $modal->id)
                            ->flatMap->hasilRepairs
                            ->sum('jumlah');
                    }

                    // Hitung Potongan
                    $selisih = $hasilIndividu - $targetWajib;
                    $potonganPerOrang = 0;

                    if ($selisih < 0 && $targetWajib > 0 && $potonganPerLembar > 0) {
                        $nominalPotongan = abs($selisih) * $potonganPerLembar;

                        // Pembulatan
                        $ribuan = floor($nominalPotongan / 1000);
                        $ratusan = $nominalPotongan % 1000;

                        if ($ratusan < 300) {
                            $potonganPerOrang = $ribuan * 1000;
                        } elseif ($ratusan < 800) {
                            $potonganPerOrang = ($ribuan * 1000) + 500;
                        } else {
                            $potonganPerOrang = ($ribuan + 1) * 1000;
                        }
                    }

                    $potonganFinal = $rp->potongan ?? $potonganPerOrang;

                    $results[] = [
                        'kodep' => $rp->pegawai->kode_pegawai ?? '-',
                        'nama' => $rp->pegawai->nama_pegawai ?? 'TANPA NAMA',
                        'masuk' => $rp->jam_masuk ? Carbon::parse($rp->jam_masuk)->format('H:i') : '',
                        'pulang' => $rp->jam_pulang ? Carbon::parse($rp->jam_pulang)->format('H:i') : '',
                        'hasil' => $labelPekerjaan,
                        'ijin' => $rp->ijin ?? '',
                        'potongan_targ' => (int) $potonganFinal,
                        'keterangan' => $rp->keterangan ?? '',
                    ];
                }
            }
        }

        return $results;
    }
}