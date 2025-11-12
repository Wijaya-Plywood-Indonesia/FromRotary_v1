<?php

namespace App\Http\Controllers;

use App\Models\HargaKayu;
use App\Models\NotaKayu;

class NotaKayuController extends Controller
{
    public function show(NotaKayu $record)
    {
        // Muat relasi yang diperlukan
        $record->load([
            'kayuMasuk.detailTurusanKayus',
            'kayuMasuk.penggunaanSupplier',
        ]);

        $detail = $record->kayuMasuk->detailTurusanKayus ?? collect();

        $jenisKayuId = optional($detail->first())->jenis_kayu_id
            ?? optional($detail->first()->jenisKayu)->id
            ?? 1;
        $grade = optional($detail->first())->grade ?? 1;
        $panjang = optional($detail->first())->panjang ?? 130;

        // === Grouping berdasarkan rentang diameter dan error handling ===
        $groupedByDiameter = $this->groupByRentangDiameter($detail, $jenisKayuId, $grade, $panjang);

        // === Hitung total dari hasil grouping ===
        // Ambil semua detail
        $details = $record->kayuMasuk->detailTurusanKayus ?? collect();

        // Hitung langsung dari semua detail
        $totalBatang = $details->sum('kuantitas');
        $totalKubikasi = $details->sum('kubikasi');

        // Hitung total harga sesuai rentang per detail
        $grandTotal = 0;

        foreach ($details as $item) {
            $idJenisKayu = $item->id_jenis_kayu ?? optional($item->jenisKayu)->id;
            $grade = $item->grade;
            $panjang = $item->panjang;
            $diameter = $item->diameter;

            $harga = HargaKayu::where('id_jenis_kayu', $idJenisKayu)
                ->where('grade', $grade)
                ->where('panjang', $panjang)
                ->where('diameter_terkecil', '<=', $diameter)
                ->where('diameter_terbesar', '>=', $diameter)
                ->value('harga_beli');

            $grandTotal += ($harga ?? 0) * $item->kubikasi * 1000;
        }

        // === Pembulatan manual (jika ada) ===
        $pembulatanManual = (int) ($record->adjustment ?? 0);

        // === Biaya turun kayu tetap dihitung walau harga 0 ===
        $biayaTurunPerM3 = 5000;
        $hasilDasar = $totalKubikasi * $biayaTurunPerM3;
        $biayaFloor = floor($hasilDasar / 1000) * 1000;
        $sisaRibuan = $grandTotal % 1000;
        $biayaTurunKayu = $biayaFloor + $sisaRibuan + 10000;

        // === Harga beli akhir ===
        $hargaBeliAkhir = $grandTotal - $biayaTurunKayu;

        // === Pembulatan ke kelipatan 5000 ===
        $mod = $hargaBeliAkhir % 5000;
        $hargaBeliAkhirBulat = $mod >= 2500
            ? $hargaBeliAkhir + (5000 - $mod)
            : $hargaBeliAkhir - $mod;

        $totalAkhir = $hargaBeliAkhirBulat + $pembulatanManual;
        $selisih = $grandTotal - $totalAkhir;

        return view('nota-kayu.print', [
            'record' => $record,
            'totalBatang' => $totalBatang,
            'totalKubikasi' => $totalKubikasi,
            'grandTotal' => $grandTotal,
            'biayaTurunKayu' => $biayaTurunKayu,
            'pembulatanManual' => $pembulatanManual,
            'totalAkhir' => $hargaBeliAkhir,
            'hargaFinal' => $totalAkhir,
            'selisih' => $selisih,
            'groupedByDiameter' => $groupedByDiameter,
        ]);
    }

    public function groupByRentangDiameter($details, $idJenisKayu, $grade, $panjang)
    {
        $rentangList = HargaKayu::where('id_jenis_kayu', $idJenisKayu)
            ->where('grade', $grade)
            ->where('panjang', $panjang)
            ->orderBy('diameter_terkecil')
            ->get();

        $hasil = collect();
        $terpakaiIds = collect();

        // 🔹 Cocokkan data dengan rentang harga yang tersedia
        foreach ($rentangList as $rentang) {
            $kelompok = $details->filter(function ($item) use ($rentang) {
                return $item->diameter >= $rentang->diameter_terkecil
                    && $item->diameter <= $rentang->diameter_terbesar;
            });

            if ($kelompok->isNotEmpty()) {
                $totalBatang = $kelompok->sum('kuantitas');
                $totalKubikasi = $kelompok->sum('kubikasi');
                $harga = $rentang->harga_beli;
                $totalHarga = $harga * $totalKubikasi;

                $hasil->push([
                    'rentang' => "{$rentang->diameter_terkecil} - {$rentang->diameter_terbesar}",
                    'batang' => $totalBatang,
                    'kubikasi' => $totalKubikasi,
                    'harga_satuan' => $harga,
                    'total_harga' => $totalHarga,
                ]);

                $terpakaiIds = $terpakaiIds->merge($kelompok->pluck('id'));
            }
        }

        // 🔹 Tangani data tanpa harga (error handling)
        $sisa = $details->whereNotIn('id', $terpakaiIds);

        if ($sisa->isNotEmpty()) {
            foreach ($sisa as $item) {
                $hasil->push([
                    'rentang' => "{$item->diameter} - {$item->diameter}",
                    'batang' => $item->kuantitas,
                    'kubikasi' => $item->kubikasi,
                    'harga_satuan' => 0,
                    'total_harga' => 0, // tidak menambah grand total
                ]);
            }
        }

        // 🔹 Urutkan hasil berdasarkan diameter agar rapi di tampilan
        return $hasil->sortBy(function ($i) {
            return (float) explode(' ', $i['rentang'])[0];
        })->values();
    }
}
