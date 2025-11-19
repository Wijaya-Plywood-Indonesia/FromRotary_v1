<?php

namespace App\Filament\Pages\LaporanProduksi\Transformers;

use Carbon\Carbon;

class ProduksiDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {
            $firstPalet = $item->detailPaletRotary->first();
            $ukuran = $firstPalet?->id_ukuran ?? 'TIDAK ADA UKURAN';
            $targetHarian = $item->detailPaletRotary->sum('total_lembar');

            $pekerja = $item->detailPegawaiRotary->map(function ($det) {
                return [
                    'id' => $det->pegawai->kode_pegawai ?? '-',
                    'nama' => $det->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $det->jam_masuk,
                    'jam_pulang' => $det->jam_pulang,
                    'ijin' => $det->ijin ?? '-',
                    'keterangan' => $det->keterangan ?? '-',
                ];
            })->toArray();

            $result[] = [
                'mesin' => $item->mesin->nama_mesin,
                'tanggal' => Carbon::parse($item->tgl_produksi)->format('d/m/Y'),
                'ukuran' => $ukuran,
                'pekerja' => $pekerja,
                'hasil' => $targetHarian,
                'kendala' => $item->kendala ?? '-',
            ];
        }

        return $result;
    }
}
