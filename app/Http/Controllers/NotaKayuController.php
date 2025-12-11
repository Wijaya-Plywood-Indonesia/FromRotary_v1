<?php

namespace App\Http\Controllers;

use App\Models\HargaKayu;
use App\Models\NotaKayu;
use Illuminate\Support\Collection;

class NotaKayuController extends Controller
{
    public function show(NotaKayu $record)
    {
        // 1. LOAD RELASI
        $record->load([
            'kayuMasuk.detailTurusanKayus.jenisKayu', // Load jenis kayu di detail agar aman
            'kayuMasuk.penggunaanSupplier',
        ]);

        $details = $record->kayuMasuk->detailTurusanKayus ?? collect();

        // Ambil atribut umum dari item pertama (Asumsi 1 nota = 1 jenis/grade/panjang)
        $firstItem = $details->first();

        $jenisKayuId = $firstItem->jenis_kayu_id
            ?? optional($firstItem->jenisKayu)->id
            ?? 1;

        $grade = $firstItem->grade ?? 1;
        $panjang = $firstItem->panjang ?? 130;

        // ============================================================
        // 2. GROUPING BERDASARKAN DIAMETER SPESIFIK (LOGIKA BARU)
        // ============================================================
        $groupedByDiameter = $this->groupByDiameterSpesifik(
            $details,
            $jenisKayuId,
            $grade,
            $panjang
        );

        // =========================
        // 3. HITUNG TOTAL & GRAND TOTAL
        // =========================
        // (Bagian ini tetap sama agar perhitungan uang tidak berubah)

        $totalBatang = $details->sum('kuantitas');

        $totalKubikasi = $details->sum(function ($item) {
            return round($item->kubikasi, 4);
        });

        $grandTotal = 0;

        // Hitung Grand Total baris per baris (Original Logic)
        foreach ($details as $item) {
            // Cari harga untuk item ini
            $harga = $this->getHargaSatuan(
                $item->id_jenis_kayu ?? optional($item->jenisKayu)->id,
                $item->grade,
                $item->panjang,
                $item->diameter
            );

            $kubikasi = round($item->kubikasi, 4);
            $grandTotal += round(($harga ?? 0) * $kubikasi * 1000);
        }

        $grandTotal = (int) round($grandTotal);

        // =========================
        // 4. BIAYA LAIN-LAIN (COPY PASTE LOGIKA ASLI)
        // =========================

        $pembulatanManual = (int) ($record->adjustment ?? 0);
        $biayaTurunPerM3 = 5000;

        $hasilDasar = round($totalKubikasi * $biayaTurunPerM3);
        $biayaFloor = floor($hasilDasar / 1000) * 1000;
        $sisaRibuan = $grandTotal % 1000;

        $biayaTurunKayu = (int) ($biayaFloor + $sisaRibuan + 10000);

        $hargaBeliAkhir = (int) round($grandTotal - $biayaTurunKayu);

        // Bulatkan ke 5000
        $mod = $hargaBeliAkhir % 5000;
        $hargaBeliAkhirBulat = $mod >= 2500
            ? $hargaBeliAkhir + (5000 - $mod)
            : $hargaBeliAkhir - $mod;

        $totalAkhir = (int) ($hargaBeliAkhirBulat + $pembulatanManual);

        // Final round
        $mod = $totalAkhir % 5000;
        $totalAkhir = $mod >= 2500
            ? $totalAkhir + (5000 - $mod)
            : $totalAkhir - $mod;

        $selisih = (int) ($grandTotal - $totalAkhir);

        return view('nota-kayu.print', [
            'record' => $record,
            'totalBatang' => $totalBatang,
            'totalKubikasi' => round($totalKubikasi, 4),
            'grandTotal' => $grandTotal,
            'biayaTurunKayu' => $biayaTurunKayu,
            'pembulatanManual' => $pembulatanManual,
            'totalAkhir' => $hargaBeliAkhir,
            'hargaFinal' => $totalAkhir,
            'selisih' => $selisih,
            // Kirim data yang sudah di-group by diameter spesifik
            'groupedByDiameter' => $groupedByDiameter,
        ]);
    }

    /**
     * LOGIKA BARU: Mengelompokkan data berdasarkan diameter spesifik (13, 14, 15...)
     * Hanya diameter yang ada datanya yang akan muncul.
     */
    private function groupByDiameterSpesifik(Collection $details, $idJenisKayu, $grade, $panjang)
    {
        // 1. Grouping collection laravel berdasarkan kolom 'diameter'
        // Hasil: [ 13 => [item1, item2], 19 => [item3] ]
        $groups = $details->groupBy('diameter');

        $hasil = collect();

        foreach ($groups as $diameter => $items) {

            // 2. Hitung Agregat per Diameter
            $batang = $items->sum('kuantitas');

            $kubikasi = $items->sum(function ($item) {
                return round($item->kubikasi, 4);
            });

            // 3. Cari Harga untuk Diameter spesifik ini
            // Kita cari di tabel harga dimana diameter ini masuk dalam range-nya
            $hargaSatuan = $this->getHargaSatuan($idJenisKayu, $grade, $panjang, $diameter);

            // 4. Hitung Total Rupiah
            $totalHarga = round($hargaSatuan * $kubikasi * 1000);

            // 5. Masukkan ke Collection hasil
            $hasil->push([
                'diameter' => $diameter, // Angka tunggal (misal: 13)
                'batang' => $batang,   // Jumlah batang (untuk Tally Mark)
                'kubikasi' => $kubikasi,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
            ]);
        }

        // 6. Urutkan berdasarkan diameter dari kecil ke besar (13, 15, 19...)
        return $hasil->sortBy('diameter')->values();
    }

    /**
     * Helper untuk mencari harga berdasarkan diameter spesifik
     */
    private function getHargaSatuan($idJenisKayu, $grade, $panjang, $diameter)
    {
        return HargaKayu::where('id_jenis_kayu', $idJenisKayu)
            ->where('grade', $grade)
            ->where('panjang', $panjang)
            // Logika: Diameter Log harus berada di antara Min dan Max di tabel Harga
            ->where('diameter_terkecil', '<=', $diameter)
            ->where('diameter_terbesar', '>=', $diameter)
            ->orderBy('diameter_terkecil', 'desc') // Ambil range paling ketat jika ada tumpang tindih
            ->value('harga_beli') ?? 0;
    }
}
