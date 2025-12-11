<?php

namespace App\Filament\Pages\LaporanPressDryer\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class DryerDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {

            /* ============================================================
             * 1. MESIN, SHIFT, TANGGAL
             * ============================================================ */

            $mesinList = $item->detailMesins
                ->pluck('mesin.nama_mesin')
                ->filter()
                ->unique();

            $namaMesin = $mesinList->isNotEmpty()
                ? $mesinList->implode(' & ')
                : 'TIDAK ADA MESIN';

            $mesinUtamaId = $item->detailMesins->first()?->id_mesin_dryer;

            $shift = strtoupper($item->shift ?? 'PAGI');
            $tanggal = Carbon::parse($item->tanggal_produksi)->format('d/m/Y');


            /* ============================================================
             * 2. DEFAULT (WAJIB)
             * ============================================================ */

            $ukuranDisplay = 'TIDAK ADA UKURAN';
            $totalHasil = 0;

            $targetHarian = 0;
            $jamKerja = 0;
            $potonganPerLembar = 0;

            $kodeUkuran = null;
            $ukuranId = null;

            $targetModel = null;


            /* ============================================================
             * 3. CEK DETAIL HASIL & CARI TARGET
             * ============================================================ */

            if ($item->detailHasils->isEmpty()) {

                $ukuranDisplay = 'BELUM INPUT HASIL';
                $totalHasil = 0;

                // ✅ TETAP CARI TARGET MESKI HASIL KOSONG
                if ($mesinUtamaId) {

                    if (stripos($namaMesin, 'DRYER') !== false) {
                        // Untuk DRYER, gunakan kode_ukuran berdasarkan shift
                        if ($shift === 'PAGI') {
                            $targetModel = Target::where('kode_ukuran', 'DRYER PAGI')->first();
                        } elseif ($shift === 'MALAM') {
                            $targetModel = Target::where('kode_ukuran', 'DRYER MALAM')->first();
                        }
                    } else {
                        // Untuk mesin lain, cari target default
                        $targetModel = Target::where('id_mesin', $mesinUtamaId)
                            ->whereNull('id_ukuran')
                            ->first();
                    }
                }

                Log::warning('PressDryer tanpa detail hasil (target tetap dicari)', [
                    'id_produksi' => $item->id,
                    'mesin' => $namaMesin,
                    'shift' => $shift,
                    'target_ditemukan' => $targetModel !== null,
                    'target' => $targetModel->target ?? 0,
                ]);

            } else {

                /* 3A. Ambil ukuran & total hasil */
                $firstHasil = $item->detailHasils->first();
                $ukuranId = $firstHasil?->id_ukuran ?? null;

                $totalHasil = $item->detailHasils->sum('isi') ?? 0;

                /* 3B. Cari target: mesin + ukuran */
                if ($mesinUtamaId) {

                    // ✅ Untuk DRYER, cari berdasarkan shift (kode_ukuran)
                    if (stripos($namaMesin, 'DRYER') !== false) {

                        // Map shift ke kode_ukuran
                        if ($shift === 'PAGI') {
                            $targetModel = Target::where('kode_ukuran', 'DRYER PAGI')->first();
                        } elseif ($shift === 'MALAM') {
                            $targetModel = Target::where('kode_ukuran', 'DRYER MALAM')->first();
                        } else {
                            $targetModel = null; // Shift tidak dikenal
                        }

                        Log::info('DEBUG Query Target DRYER', [
                            'mesin' => $namaMesin,
                            'shift' => $shift,
                            'kode_ukuran_digunakan' => $shift === 'PAGI' ? 'DRYER PAGI' : ($shift === 'MALAM' ? 'DRYER MALAM' : 'UNKNOWN'),
                            'found' => $targetModel !== null,
                            'target' => $targetModel->target ?? 0,
                            'jam' => $targetModel->jam ?? 0,
                        ]);

                    } else {
                        // ✅ Untuk mesin lain (SANJI, YUEQUN, MERANTI, dll)
                        // Cari berdasarkan id_mesin + id_ukuran
                        $targetModel = Target::where('id_mesin', $mesinUtamaId)
                            ->when($ukuranId !== null, function ($q) use ($ukuranId) {
                                return $q->where('id_ukuran', $ukuranId);
                            })
                            ->first();

                        // Fallback ke target default (id_ukuran = NULL)
                        if (!$targetModel) {
                            $targetModel = Target::where('id_mesin', $mesinUtamaId)
                                ->whereNull('id_ukuran')
                                ->first();
                        }

                        Log::info('DEBUG Query Target Non-DRYER', [
                            'mesin' => $namaMesin,
                            'mesin_id' => $mesinUtamaId,
                            'ukuran_id' => $ukuranId,
                            'found' => $targetModel !== null,
                            'target' => $targetModel->target ?? 0,
                        ]);
                    }

                } else {
                    // Mesin ID tidak ditemukan
                    Log::warning('DEBUG: mesin_utama_id adalah NULL', [
                        'id_produksi' => $item->id,
                        'mesin' => $namaMesin,
                    ]);
                }
            }

            /* ============================================================
             * 3C. FALLBACK JIKA TARGET BELUM DITEMUKAN
             * ============================================================ */
            if ($targetModel === null && $mesinUtamaId) {
                // Last resort: cari target default
                if (stripos($namaMesin, 'DRYER') !== false) {
                    if ($shift === 'PAGI') {
                        $targetModel = Target::where('kode_ukuran', 'DRYER PAGI')->first();
                    } elseif ($shift === 'MALAM') {
                        $targetModel = Target::where('kode_ukuran', 'DRYER MALAM')->first();
                    }
                } else {
                    $targetModel = Target::where('id_mesin', $mesinUtamaId)
                        ->whereNull('id_ukuran')
                        ->first();
                }
            }

            // ✅ Update nilai target setelah semua pengecekan
            $targetHarian = $targetModel->target ?? 0;
            $jamKerja = $targetModel->jam ?? 0;
            $potonganPerLembar = $targetModel->potongan ?? 0;
            $kodeUkuran = $targetModel->kode_ukuran ?? null;

            /* ============================================================
             * 3D. FORMAT UKURAN
             * ============================================================ */
            if ($kodeUkuran && $kodeUkuran !== '') {

                // Gunakan regex yang lebih aman (hanya potong prefix)
                $ukuranDisplay = preg_replace(
                    '/^(SPINDLESS|YUEQUN|MERANTI|SANJI|DRYER\s*PAGI|DRYER\s*MALAM|PRESS)\s*/i',
                    '',
                    $kodeUkuran
                );

                $ukuranDisplay = trim($ukuranDisplay) ?: $kodeUkuran;

            } else {
                // Jika tidak ada hasil, tetap tampilkan info
                if ($totalHasil === 0) {
                    $ukuranDisplay = 'BELUM INPUT HASIL';
                } else {
                    $ukuranDisplay = "UKURAN BELUM DISET (id: {$ukuranId})";
                }
            }


            /* ============================================================
             * 4. HITUNG POTONGAN TARGET (PEMBULATAN 3 TINGKAT)
             * ============================================================ */

            $selisihProduksi = $totalHasil - $targetHarian;
            $jumlahPekerja = $item->detailPegawais->count();

            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($targetHarian > 0 && $selisihProduksi < 0 && $potonganPerLembar > 0) {
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;

                if ($jumlahPekerja > 0) {
                    $potonganPerOrangRaw = $potonganTotal / $jumlahPekerja;

                    // ✅ Pembulatan 3 tingkat: 0-290 | 291-749 | 750-999
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
            }


            /* ============================================================
             * 5. DETAIL PEKERJA
             * ============================================================ */

            $pekerja = $item->detailPegawais->map(function ($det) use ($potonganPerOrang) {

                return [
                    'id' => $det->pegawai->kode_pegawai ?? '-',
                    'nama' => $det->pegawai->nama_pegawai ?? '-',

                    'jam_masuk' => $det->masuk ?? '-',
                    'jam_pulang' => $det->pulang ?? '-',
                    'ijin' => $det->ijin ?? '-',

                    'keterangan' => $det->keterangan ?? '-',
                    'pot_target' => $potonganPerOrang,
                    'pot_target_formatted' => 'Rp ' . number_format($potonganPerOrang, 0, ',', '.'),
                ];

            })->toArray();


            /* ============================================================
             * 6. MASUKKAN KE RESULT
             * ============================================================ */

            $result[] = [
                'mesin' => $namaMesin . ' - ' . $shift,
                'mesin_only' => $namaMesin,
                'shift' => $shift,
                'tanggal' => $tanggal,

                'ukuran' => $ukuranDisplay,
                'ukuran_id' => $ukuranId,
                'kode_ukuran_raw' => $kodeUkuran,

                'pekerja' => $pekerja,
                'kendala' => $item->kendala ?? '-',

                'jam_kerja' => $jamKerja,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,

                'potongan_total' => $potonganTotal,
                'potongan_per_orang' => $potonganPerOrang,

                'has_target' => $targetModel !== null,
            ];


            /* ============================================================
             * 7. LOG DEBUG
             * ============================================================ */

            Log::info('DryerDataMap - Final Result', [
                'mesin' => $namaMesin,
                'shift' => $shift,
                'ukuran_id' => $ukuranId,
                'target_ditemukan' => $targetModel !== null,
                'target' => $targetHarian,
                'jam_kerja' => $jamKerja,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,
                'potongan_per_orang' => $potonganPerOrang,
            ]);
        }

        return $result;
    }
}