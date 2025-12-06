<?php

namespace App\Filament\Pages\LaporanRepairs\Transformers;

use Carbon\Carbon;

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

                $pekerja = [];
                foreach ($produksi->rencanaPegawais as $rp) {

                    $pegawai = $rp->pegawai ?? null;

                    if ($pegawai) {
                        // Ambil jam_masuk / jam_pulang dari model RencanaPegawai ($rp)
                        $jamMasukRaw = $rp->jam_masuk ?? null;
                        $jamPulangRaw = $rp->jam_pulang ?? null;

                        $jamMasuk = $jamMasukRaw
                            ? Carbon::parse($jamMasukRaw)->format('H:i')
                            : '-';

                        $jamPulang = $jamPulangRaw
                            ? Carbon::parse($jamPulangRaw)->format('H:i')
                            : '-';

                        $pekerja[] = [
                            'id' => $pegawai->kode_pegawai ?? '-',
                            'nama' => $pegawai->nama_pegawai ?? '-',
                            'jam_masuk' => $jamMasuk,
                            'jam_pulang' => $jamPulang,
                            'ijin' => $rp->ijin ?? '-',
                            'keterangan' => $rp->keterangan ?? '-',
                            // hasil pekerja per modal: ambil rencanaRepair milik rencanaPegawai untuk modal ini
                            'hasil' => $rp->rencanaRepairs
                                ->where('id_modal_repair', $modal->id)
                                ->flatMap
                                ->hasilRepairs
                                ->sum('jumlah'),
                        ];
                    }
                }

                // total hasil modal (semua hasil dari rencanaRepairs yang terkait modal)
                $totalHasil = $modal->rencanaRepairs
                    ->flatMap
                    ->hasilRepairs
                    ->sum('jumlah');

                $modalData[] = [
                    'mesin' => $modal->nomor_meja ?? '-',
                    'ukuran' => $ukuran,
                    'jenis_kayu' => $jenisKayu,
                    'pekerja' => $pekerja,
                    'hasil' => $totalHasil,
                    'target' => $modal->target ?? 0,
                    'selisih' => $totalHasil - ($modal->target ?? 0),
                    'jam_kerja' => $modal->jam_kerja ?? 0,
                    'tanggal' => $tanggal,
                ];
            }

            // gabungkan per-modal ke hasil akhir (sama seperti implementasimu sebelumnya)
            $result = array_merge($result, $modalData);
        }

        return $result;
    }
}
