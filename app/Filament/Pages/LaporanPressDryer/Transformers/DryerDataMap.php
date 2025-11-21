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

            $mesinUtamaId = $item->detailMesins->first()?->id_mesin;

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
             * 3. CEK DETAIL HASIL
             * ============================================================ */

            if ($item->detailHasils->isEmpty()) {

                $ukuranDisplay = 'BELUM INPUT HASIL';

                Log::warning('PressDryer tanpa detail hasil', [
                    'id_produksi' => $item->id,
                    'mesin' => $namaMesin,
                    'shift' => $shift,
                    'tanggal' => $tanggal,
                ]);

            } else {

                /* 3A. Ambil ukuran & total hasil */
                $firstHasil = $item->detailHasils->first();
                $ukuranId = $firstHasil?->id_ukuran ?? null;

                $totalHasil = $item->detailHasils->sum('isi') ?? 0;

                /* 3B. Cari target: mesin + ukuran */
                if ($mesinUtamaId) {

                    // Ambil target dengan ukuran jika ukuran tersedia
                    $targetModel = Target::where('id_mesin', $mesinUtamaId)
                        ->when($ukuranId !== null, function ($q) use ($ukuranId) {
                            return $q->where('id_ukuran', $ukuranId);
                        })
                        ->first();

                    // Jika ukuran null atau target ukuran tidak ditemukan → fallback ke target default mesin
                    if (!$targetModel) {
                        $targetModel = Target::where('id_mesin', $mesinUtamaId)
                            ->whereNull('id_ukuran')
                            ->first();
                    }
                }

                /* 3C. Ambil nilai target (HARUS ADA DEFAULT) */
                $targetHarian = $targetModel->target ?? 0;
                $jamKerja = $targetModel->jam ?? 0;
                $potonganPerLembar = $targetModel->potongan ?? 0;
                $kodeUkuran = $targetModel->kode_ukuran ?? null;

                /* 3D. Format ukuran */
                if ($kodeUkuran && $kodeUkuran !== '') {

                    // Gunakan regex yang lebih aman (hanya potong prefix)
                    $ukuranDisplay = preg_replace(
                        '/^(SPINDLESS|YUEQUN|MERANTI|SANJI|DRYER\s*PAGI|PRESS)\s*/i',
                        '',
                        $kodeUkuran
                    );

                    $ukuranDisplay = trim($ukuranDisplay) ?: $kodeUkuran;

                } else {
                    $ukuranDisplay = "UKURAN BELUM DISET (id: {$ukuranId})";
                }
            }


            /* ============================================================
             * 4. HITUNG POTONGAN TARGET
             * ============================================================ */

            $selisihProduksi = $totalHasil - $targetHarian;
            $jumlahPekerja = $item->detailPegawais->count();

            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($targetHarian > 0 && $selisihProduksi < 0 && $potonganPerLembar > 0) {
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;
                $potonganPerOrang = $jumlahPekerja > 0
                    ? round($potonganTotal / $jumlahPekerja)
                    : 0;
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
                    'pot_target' => round($potonganPerOrang, 2),
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

            Log::info('DryerDataMap', [
                'mesin' => $namaMesin,
                'shift' => $shift,
                'ukuran_id' => $ukuranId,
                'target_ditemukan' => $targetModel !== null,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
            ]);
        }

        return $result;
    }
}
