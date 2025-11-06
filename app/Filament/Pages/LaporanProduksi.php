<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ProduksiRotary;
use BackedEnum;
use UnitEnum;
use App\Exports\LaporanProduksiExport;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class LaporanProduksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.laporan-produksi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Rotary';

    public $dataProduksi = [];

    public function mount(): void
    {
        $this->loadAllData();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportToExcel'),
        ];
    }

    public function loadAllData()
    {
        $produksiList = ProduksiRotary::with([
            'mesin',
            'detailLahanRotary.lahan',
            'detailPaletRotary.ukuran',
            'detailPegawaiRotary.pegawai',
            'detailGantiPisauRotary',
        ])
            ->orderBy('tgl_produksi', 'desc')
            ->limit(10)
            ->get();

        $this->dataProduksi = [];

        foreach ($produksiList as $produksi) {
            // ======== BAHAN =========
            $bahan = [];
            $totalBatang = 0;
            foreach ($produksi->detailLahanRotary as $detail) {
                $batang = $detail->jumlah_batang ?? 0;
                $bahan[] = [
                    'lahan' => $detail->lahan->kode_lahan ?? '-',
                    'batang' => $batang,
                ];
                $totalBatang += $batang;
            }

            // ======== HASIL PRODUKSI =========
            $hasil = [];
            $totalPalet = 0;
            $totalLembar = 0;
            $totalM3 = 0;

            foreach ($produksi->detailPaletRotary as $detail) {
                $ukuran = $detail->ukuran->nama_ukuran ?? 'Unknown';
                $kw = $detail->kw ?? 'Unknown';

                if (!isset($hasil[$ukuran])) {
                    $hasil[$ukuran] = [];
                }

                if (!isset($hasil[$ukuran][$kw])) {
                    $hasil[$ukuran][$kw] = ['palet' => 0, 'lembar' => 0];
                }

                $palet = $detail->total ?? 0;
                $lembar = $detail->total_lembar ?? 0;

                $hasil[$ukuran][$kw]['palet'] += $palet;
                $hasil[$ukuran][$kw]['lembar'] += $lembar;

                $totalPalet += $palet;
                $totalLembar += $lembar;
            }

            // Hitung total M3
            $totalM3 = $totalBatang * 0.5;

            // ======== PEKERJA =========
            $pekerja = [];
            $jamKerjaTotal = 0;

            foreach ($produksi->detailPegawaiRotary as $detail) {
                $jamMasuk = $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk) : null;
                $jamPulang = $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang) : null;

                $jamKerja = 0;
                if ($jamMasuk && $jamPulang) {
                    $jamKerja = $jamMasuk->diffInHours($jamPulang);
                    $jamKerjaTotal += $jamKerja;
                }

                $pekerja[] = [
                    'id' => $detail->pegawai->id ?? '-',
                    'nama' => $detail->pegawai->nama ?? '-',
                    'jam_masuk' => $jamMasuk ? $jamMasuk->format('H:i') : '-',
                    'jam_pulang' => $jamPulang ? $jamPulang->format('H:i') : '-',
                    'ijin' => $detail->ijin ?? '-',
                    'pot_target' => $detail->pot_target ?? 0,
                    'keterangan' => $detail->keterangan ?? '-',
                    'jam_kerja' => $jamKerja,
                ];
            }

            // Hitung rata-rata jam kerja
            $jumlahPekerja = count($pekerja);
            $rataRataJamKerja = $jumlahPekerja > 0 ? round($jamKerjaTotal / $jumlahPekerja, 1) : 0;

            // ======== GANTI PISAU =========
            $gantiPisau = [];
            foreach ($produksi->detailGantiPisauRotary as $detail) {
                $jamMulai = $detail->jam_mulai ? \Carbon\Carbon::parse($detail->jam_mulai) : null;
                $jamSelesai = $detail->jam_selesai ? \Carbon\Carbon::parse($detail->jam_selesai) : null;

                $gantiPisau[] = [
                    'jam_mulai' => $jamMulai ? $jamMulai->format('H:i') : '-',
                    'jam_selesai' => $jamSelesai ? $jamSelesai->format('H:i') : '-',
                    'durasi' => ($jamMulai && $jamSelesai) ? $jamMulai->diffInMinutes($jamSelesai) . ' menit' : '-',
                ];
            }

            // ======== SUMMARY =========
            $summary = [
                'total_batang' => $totalBatang,
                'total_palet' => $totalPalet,
                'total_lembar' => $totalLembar,
                'total_m3' => round($totalM3, 2),
                'jam_kerja' => $rataRataJamKerja,
                'jumlah_pekerja' => $jumlahPekerja,
                'total_pot_target' => collect($pekerja)->sum('pot_target'),
            ];

            // ======== Tambahkan ke daftar data produksi =========
            $this->dataProduksi[] = [
                'id' => $produksi->id,
                'tanggal' => \Carbon\Carbon::parse($produksi->tgl_produksi)->format('d/m/Y'),
                'mesin' => $produksi->mesin->nama_mesin ?? 'Rotary',
                'bahan' => $bahan,
                'hasil' => $hasil,
                'pekerja' => $pekerja,
                'kendala' => $produksi->kendala ?? 'Tidak ada kendala.',
                'ganti_pisau' => $gantiPisau,
                'summary' => $summary,
            ];
        }
    }

    public function exportToExcel()
    {
        $fileName = 'laporan-produksi-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new LaporanProduksiExport($this->dataProduksi), $fileName);
    }
}