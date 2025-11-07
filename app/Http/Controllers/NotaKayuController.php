<?php

namespace App\Http\Controllers;

use App\Models\NotaKayu;

class NotaKayuController extends Controller
{
    public function show(NotaKayu $record)
    {
        // muat relasi
        $record->load('kayuMasuk.detailMasukanKayu', 'kayuMasuk.penggunaanSupplier');
        //Total Batang
        $totalBatang = $record->kayuMasuk->detailMasukanKayu->sum('jumlah_batang');
        // Hitung dasar
        $totalKubikasi = $record->kayuMasuk->detailMasukanKayu->sum('kubikasi');
        //    $grandTotal = (int) $record->kayuMasuk->detailMasukanKayu->sum('total_harga'); // dalam rupiah, integer
        $grandTotal = $record->kayuMasuk->detailMasukanKayu->sum('total_harga');
        $pembulatanManual = (int) ($record->adjustment ?? 0);

        // === Hitung biaya turun kayu (versi Excel) ===
        $biayaTurunPerM3 = 5000;
        $hasilDasar = $totalKubikasi * $biayaTurunPerM3;
        $biayaFloor = floor($hasilDasar / 1000) * 1000;
        $sisaRibuan = $grandTotal % 1000;
        $biayaTurunKayu = $biayaFloor + $sisaRibuan + 10000;

        // === Hitung harga beli akhir ===
        $hargaBeliAkhir = $grandTotal - $biayaTurunKayu;

        // === Pembulatan ke kelipatan 5000 dengan threshold 2500 ===
        $mod = $hargaBeliAkhir % 5000;

        if ($mod >= 2500) {
            $hargaBeliAkhirBulat = $hargaBeliAkhir + (5000 - $mod); // Naik ke atas
        } else {
            $hargaBeliAkhirBulat = $hargaBeliAkhir - $mod; // Turun ke bawah
        }

        // Tambahkan pembulatan manual jika diperlukan
        $totalAkhir = $hargaBeliAkhirBulat + $pembulatanManual;

        $selisih = $grandTotal - $totalAkhir;

        return view('nota-kayu.print', [
            'record' => $record,
            'totalKubikasi' => $totalKubikasi,
            'grandTotal' => $grandTotal,
            'biayaTurunKayu' => $biayaTurunKayu,
            'pembulatanManual' => $pembulatanManual,
            'totalAkhir' => $hargaBeliAkhir,
            'hargaFinal' => $totalAkhir,
            'totalBatang' => $totalBatang,
            'selisih' => $selisih,
        ]);
    }


}