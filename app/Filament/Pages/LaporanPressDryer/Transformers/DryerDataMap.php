<?php

namespace App\Filament\Pages\LaporanPressDryer\Transformers;

use Carbon\Carbon;
use App\Models\Target; // sesuaikan nama model targetnya
use Illuminate\Support\Facades\Log;

class DryerDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {

            // Nama mesin diambil dari detail_mesins (bisa banyak mesin per shift)
            $mesinList = $item->detailMesins->pluck('mesin.nama_mesin')->filter()->unique();
            $namaMesin = $mesinList->isNotEmpty()
                ? $mesinList->implode(' & ')
                : 'TIDAK ADA MESIN';

            $shift = strtoupper($item->shift ?? 'PAGI');
            $tanggal = Carbon::parse($item->tanggal_produksi)->format('d/m/Y');

            // DEFAULT VALUE – WAJIB ada sebelum if-else
            $ukuranDisplay = 'TIDAK ADA UKURAN';
            $totalHasil = 0;
            $targetHarian = 0;
            $jamKerja = 0;
            $potonganPerLembar = 0;
            $kodeUkuran = null;
            $targetModel = null;
            $ukuranId = null;

            // ---------------------------------------------------------
            // 2. CEK DETAIL HASIL (palet)
            // ---------------------------------------------------------
            if ($item->detailHasils->isEmpty()) {

                $ukuranDisplay = 'BELUM INPUT HASIL';

                Log::warning('PressDryer tanpa detail hasil', [
                    'id_produksi' => $item->id,
                    'mesin' => $namaMesin,
                    'shift' => $shift,
                    'tanggal' => $tanggal,
                ]);

            } else {
                $firstHasil = $item->detailHasils->first();
                $ukuranId = $firstHasil?->id_ukuran;

                $totalHasil = $item->detailHasils->sum('isi') ?? 0;        // jumlah lembar

                // Cari target berdasarkan shift + ukuran
                $targetModel = Target::where('shift', $item->shift)
                    ->where('id_ukuran', $ukuranId)
                    ->first();

                if (!$targetModel) {
                    $targetModel = Target::where('shift', $item->shift)
                        ->whereNull('id_ukuran')
                        ->first();
                }

                $targetHarian = $targetModel?->target_lembar ?? 0;
                $jamKerja = $targetModel?->jam_kerja ?? 8;
                $potonganPerLembar = $targetModel?->potongan_per_lembar ?? 0;
                $kodeUkuran = $targetModel?->kode_ukuran;

                // Format kode ukuran (sesuai pola Rotary)
                if ($kodeUkuran && trim($kodeUkuran) !== '') {
                    $ukuranDisplay = preg_replace('/^(SPINDLESS|YUEQUN|MERANTI|SANJI|DRYER\s*PAGI|PRESS)/i', '', $kodeUkuran);
                    $ukuranDisplay = trim($ukuranDisplay) ?: $kodeUkuran;
                } else {
                    $ukuranDisplay = 'UKURAN BELUM DISET (id: ' . $ukuranId . ')';
                }
            }

            // Hitung selisih & potongan
            $selisihProduksi = $totalHasil - $targetHarian;

            $jumlahPekerja = $item->detailPegawais->count();
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($targetHarian > 0 && $selisihProduksi < 0 && $potonganPerLembar > 0) {
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;
                $potonganPerOrang = $jumlahPekerja > 0 ? round($potonganTotal / $jumlahPekerja) : 0;
            }

            $pekerja = $item->detailPegawais->map(function ($det) use ($potonganPerOrang) {
                return [
                    'id' => $det->pegawai->kode_pegawai ?? '-',
                    'nama' => $det->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $det->jam_masuk ?? '-',
                    'jam_pulang' => $det->jam_pulang ?? '-',
                    'ijin' => $det->ijin ?? '-',
                    'keterangan' => $det->keterangan ?? '-',
                    'pot_target' => round($potonganPerOrang, 2),
                ];
            })->toArray();

            $result[] = [
                'mesin' => $namaMesin . ' - ' . $shift,   // ex: PRESS 1 & DRYER A - PAGI
                'mesin_only' => $namaMesin,
                'shift' => $shift,
                'tanggal' => $tanggal,
                'ukuran' => $ukuranDisplay,
                'pekerja' => $pekerja,
                'kendala' => $item->kendala ?? '-',
                'jam_kerja' => $jamKerja,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,
                'potongan_total' => $potonganTotal,
                'potongan_per_orang' => round($potonganPerOrang, 2),
                'has_target' => $targetModel !== null,
                'kode_ukuran_raw' => $kodeUkuran,
                'ukuran_id' => $ukuranId,
            ];

            Log::info('DryerDataMap', [
                'mesin' => $namaMesin,
                'shift' => $shift,
                'ukuran_id' => $ukuranId,
                'kode_ukuran' => $ukuranDisplay,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
            ]);
        }

        return $result;
    }
}