<?php

namespace App\Filament\Pages\LaporanProduksi\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class ProduksiDataMap
{

    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {

            $namaMesin = $item->mesin->nama_mesin ?? 'TIDAK ADA MESIN';
            $tanggal = Carbon::parse($item->tgl_produksi)->format('d/m/Y');

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
            // 2. CEK DETAIL PALET
            // ---------------------------------------------------------
            if ($item->detailPaletRotary->isEmpty()) {

                $ukuranDisplay = 'BELUM INPUT PALET';

                Log::warning('Produksi tanpa detail palet', [
                    'id_produksi' => $item->id,
                    'mesin' => $namaMesin,
                    'tanggal' => $tanggal,
                ]);

            } else {
                $firstPalet = $item->detailPaletRotary->first();
                $ukuranId = $firstPalet?->id_ukuran;

                $totalHasil = $item->detailPaletRotary->sum('total_lembar') ?? 0;

                // Cari target
                $targetModel = Target::where('id_mesin', $item->id_mesin)
                    ->where('id_ukuran', $ukuranId)
                    ->first();

                if (!$targetModel) {
                    $targetModel = Target::where('id_mesin', $item->id_mesin)
                        ->whereNull('id_ukuran')
                        ->first();
                }

                $targetHarian = $targetModel?->target;
                $jamKerja = $targetModel?->jam;
                $potonganPerLembar = $targetModel?->potongan ?? 0;
                $kodeUkuran = $targetModel?->kode_ukuran;

                // Format kode ukuran
                if ($kodeUkuran && trim($kodeUkuran) !== '') {
                    $ukuranDisplay = preg_replace('/^(SPINDLESS|YUEQUN|MERANTI|SANJI|DRYER\s*PAGI)/i', '', $kodeUkuran);
                    $ukuranDisplay = trim($ukuranDisplay) ?: $kodeUkuran;
                } else {
                    $ukuranDisplay = 'UKURAN BELUM DISET (id: ' . $ukuranId . ')';
                }
            }

            // Sekarang aman – semua variabel pasti ada
            $selisihProduksi = $totalHasil - $targetHarian;

            $jumlahPekerja = $item->detailPegawaiRotary->count();
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($targetHarian > 0 && $selisihProduksi < 0 && $potonganPerLembar > 0) {
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;
                $potonganPerOrang = $jumlahPekerja > 0 ? round($potonganTotal / $jumlahPekerja) : 0;
            }

            $pekerja = $item->detailPegawaiRotary->map(function ($det) use ($potonganPerOrang) {
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
                'mesin' => $namaMesin,
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

            Log::info('ProduksiDataMap', [
                'mesin' => $namaMesin,
                'ukuran_id' => $ukuranId,
                'kode_ukuran' => $ukuranDisplay,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
            ]);
        }

        return $result;
    }
}