<?php

namespace App\Filament\Pages\LaporanRepairs\Transformers;

use Carbon\Carbon;
use App\Models\Target;

class RepairDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $produksi) {

            $tanggal = Carbon::parse($produksi->tanggal)->format('d/m/Y');
            $modalData = [];

            foreach ($produksi->modalRepairs as $modal) {

                $ukuran = $modal->ukuran->nama_ukuran ?? 'TIDAK ADA UKURAN';
                $jenisKayu = $modal->jenisKayu->nama_kayu ?? 'TIDAK ADA JENIS';

                // KW Default 1 jika kosong
                $kw = $modal->kw ?? $modal->kualitas ?? 1;

                // -----------------------------------------------------------
                // 1. CARI TARGET SPESIFIK (Cara Ketat)
                // -----------------------------------------------------------
                // Mencari yang KW-nya persis sama dengan input (misal KW 1)
                $targetModel = Target::where('id_mesin', $produksi->id_mesin)
                    ->where('id_ukuran', $modal->id_ukuran)
                    ->where('id_jenis_kayu', $modal->id_jenis_kayu)
                    ->where('kode_ukuran', 'LIKE', '%' . $kw . 's')
                    ->first();

                // -----------------------------------------------------------
                // 2. CARI TARGET CADANGAN (Fallback) - INI SOLUSINYA
                // -----------------------------------------------------------
                // Jika cara ketat gagal (return null), kita cari target apa saja
                // yang penting Mesin, Ukuran, dan Kayunya COCOK.
                if (!$targetModel) {
                    $targetModel = Target::where('id_mesin', $produksi->id_mesin)
                        ->where('id_ukuran', $modal->id_ukuran)
                        ->where('id_jenis_kayu', $modal->id_jenis_kayu)
                        ->where('kode_ukuran', 'LIKE', 'REPAIR%') // Pastikan kode depannya REPAIR
                        ->first();
                }

                // Ambil data dari targetModel yang ditemukan
                // Karena pakai fallback, ini PASTI terisi (selama data target dibuat)
                $kodeUkuranFound = $targetModel ? $targetModel->kode_ukuran : null;
                $targetHarian = $targetModel ? $targetModel->target : ($modal->target ?? 0);
                $jamProduksi = $targetModel ? $targetModel->jam : ($modal->jam_kerja ?? 0);
                $potonganPerLembar = $targetModel ? $targetModel->potongan : 0;

                $totalHasil = $modal->rencanaRepairs
                    ->flatMap
                    ->hasilRepairs
                    ->sum('jumlah');

                $selisih = $totalHasil - $targetHarian;

                // --- HITUNG POTONGAN ---
                $potonganPerOrang = 0;
                $jumlahPekerja = $produksi->rencanaPegawais->filter(fn($rp) => $rp->pegawai)->count();

                if ($targetHarian > 0 && $selisih < 0 && $potonganPerLembar > 0 && $jumlahPekerja > 0) {
                    $totalDenda = abs($selisih) * $potonganPerLembar;
                    $potonganPerOrangRaw = $totalDenda / $jumlahPekerja;

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

                // --- MAPPING PEKERJA ---
                $pekerja = [];
                $nomorMeja = '-';

                foreach ($produksi->rencanaPegawais as $rp) {
                    $pegawai = $rp->pegawai ?? null;
                    if ($pegawai) {
                        $jamMasukRaw = $rp->jam_masuk ?? null;
                        $jamPulangRaw = $rp->jam_pulang ?? null;
                        $jamMasuk = $jamMasukRaw ? Carbon::parse($jamMasukRaw)->format('H:i') : '-';
                        $jamPulang = $jamPulangRaw ? Carbon::parse($jamPulangRaw)->format('H:i') : '-';

                        if (!empty($rp->nomor_meja)) {
                            $nomorMeja = $rp->nomor_meja;
                        }

                        $pekerja[] = [
                            'id' => $pegawai->kode_pegawai ?? '-',
                            'nama' => $pegawai->nama_pegawai ?? '-',
                            'jam_masuk' => $jamMasuk,
                            'jam_pulang' => $jamPulang,
                            'ijin' => $rp->ijin ?? '-',
                            'keterangan' => $rp->keterangan ?? '-',
                            'nomor_meja' => $rp->nomor_meja ?? '-',
                            'pot_target' => $potonganPerOrang,
                            'hasil' => $rp->rencanaRepairs
                                ->where('id_modal_repair', $modal->id)
                                ->flatMap
                                ->hasilRepairs
                                ->sum('jumlah'),
                        ];
                    }
                }

                $modalData[] = [
                    'nomor_meja' => $nomorMeja,
                    'ukuran' => $ukuran,
                    'jenis_kayu' => $jenisKayu,
                    // DATA INI YANG DITAMPILKAN DI HEADER BLADE
                    'kode_ukuran' => $kodeUkuranFound,
                    'pekerja' => $pekerja,
                    'hasil' => $totalHasil,
                    'target' => $targetHarian,
                    'jam_kerja' => $jamProduksi,
                    'selisih' => $selisih,
                    'tanggal' => $tanggal,
                ];
            }

            $result = array_merge($result, $modalData);
        }

        return $result;
    }
}