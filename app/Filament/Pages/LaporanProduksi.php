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
    public $summary = [];

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
        // Ambil semua data produksi dengan relasi
        $produksiList = ProduksiRotary::with([
            'mesin',
            'detailLahanRotary.lahan',
            'detailLahanRotary.jenisKayu',
            'detailPaletRotary.ukuran',
            'detailPegawaiRotary.pegawai',
        ])
            ->orderBy('tgl_produksi', 'desc')
            ->limit(10) // Ambil 10 data terakhir
            ->get();

        $this->dataProduksi = [];

        foreach ($produksiList as $produksi) {
            $this->dataProduksi[] = [
                'tanggal' => \Carbon\Carbon::parse($produksi->tgl_produksi)->format('d/m/Y'),
                'mesin' => $produksi->mesin->nama_mesin ?? $produksi->id_mesin,
                'bahan' => $this->getBahanData($produksi),
                'hasil' => $this->getHasilData($produksi),
                'pekerja' => $this->getPekerjaData($produksi),
                'kendala' => $produksi->kendala,
                'summary' => $this->calculateSummary($produksi),
            ];
        }

        // Hitung summary keseluruhan
        $this->calculateOverallSummary();
    }

    protected function getBahanData($produksi)
    {
        $data = [];

        foreach ($produksi->detailLahanRotary as $detail) {
            $data[] = [
                'lahan' => $detail->lahan->kode_lahan ?? '-',
                'batang' => $detail->jumlah_batang,
                'jenis_kayu' => $detail->jenisKayu->nama_kayu ?? '-',
            ];
        }

        return $data;
    }

    protected function getHasilData($produksi)
    {
        $hasil = [];

        // Kumpulkan semua detail palet
        foreach ($produksi->detailPaletRotary as $detail) {
            if (!$detail->ukuran || !$detail->ukuran->kubikasi) {
                continue;
            }

            $kubikasiCm3 = $detail->ukuran->kubikasi;
            $m3PerLembar = $kubikasiCm3 / 1_000_000;
            $kw = $detail->kw ?? 'Unknown';

            // Ambil kode_kayu dari lahan pertama
            $kodeKayu = '-';
            if ($produksi->detailLahanRotary->isNotEmpty()) {
                $lahan = $produksi->detailLahanRotary->first();
                if ($lahan->jenisKayu && $lahan->jenisKayu->kode_kayu) {
                    $kodeKayu = $lahan->jenisKayu->kode_kayu;
                }
            }

            $key = $kubikasiCm3 . '|' . $kw . '|' . $kodeKayu;

            if (!isset($hasil[$key])) {
                $hasil[$key] = [
                    'ukuran' => number_format($kubikasiCm3),
                    'kw' => $kw,
                    'jenis_kayu' => $kodeKayu,
                    'lembar' => 0,
                    'total_m3' => 0.0,
                ];
            }

            $lembar = $detail->total_lembar ?? 0;
            $hasil[$key]['lembar'] += $lembar;
            $hasil[$key]['total_m3'] += $lembar * $m3PerLembar;
        }

        // TOTAL SEMUA KW DI LAHAN (untuk kolom "Total")
        $totalLembarSemuaKW = collect($hasil)->sum('lembar');
        $totalM3SemuaKW = collect($hasil)->sum('total_m3');

        // Tambahkan baris khusus untuk TOTAL
        if ($totalLembarSemuaKW > 0) {
            $hasil['TOTAL_SEMUA_KW'] = [
                'ukuran' => '',
                'kw' => '',
                'jenis_kayu' => '',
                'lembar' => $totalLembarSemuaKW,
                'total_m3' => $totalM3SemuaKW,
            ];
        }

        return $hasil;
    }
    protected function getPekerjaData($produksi)
    {
        $data = [];

        foreach ($produksi->detailPegawaiRotary as $detail) {
            $data[] = [
                'id' => $detail->id_pegawai ?? '-',
                'nama' => $detail->pegawai->nama_pegawai ?? '-',
                'jam_masuk' => $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') : '-',
                'jam_pulang' => $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') : '-',
                'ijin' => $detail->ijin ?? '-',
                'pot_target' => number_format((float) str_replace('.', '', $detail->pot_target ?? 0), 0, '', '.'), // Bersihkan format
                'keterangan' => $detail->keterangan ?? '-',
            ];
        }

        return $data;
    }

    protected function calculateSummary($produksi)
    {
        $totalBatang = $produksi->detailLahanRotary->sum('jumlah_batang');
        $totalLembar = 0;
        $totalM3 = 0.0;

        foreach ($produksi->detailPaletRotary as $detail) {
            if (!$detail->ukuran)
                continue;
            $lembar = (int) ($detail->total_lembar ?? 0);
            $kubikasiCm3 = $detail->ukuran->kubikasi;
            $totalLembar += $lembar;
            $totalM3 += ($kubikasiCm3 * $lembar) / 1_000_000;
        }

        $pekerjaFirst = $produksi->detailPegawaiRotary->first();
        $jamKerja = 0;
        if ($pekerjaFirst && $pekerjaFirst->jam_masuk && $pekerjaFirst->jam_pulang) {
            $masuk = \Carbon\Carbon::parse($pekerjaFirst->jam_masuk);
            $pulang = \Carbon\Carbon::parse($pekerjaFirst->jam_pulang);
            $jamKerja = $pulang->diffInHours($masuk);
        }

        return [
            'total_batang' => $totalBatang,
            'total_lembar' => $totalLembar,
            'total_m3' => $totalM3,
            'jam_kerja' => $jamKerja,
            'jumlah_pekerja' => $produksi->detailPegawaiRotary->count(),
            'total_target_harian' => collect($produksi->detailPegawaiRotary)->sum(fn($p) => (float) str_replace('.', '', $p->pot_target ?? 0)),
            'total_status_harian' => $totalLembar,
        ];
    }

    protected function calculateOverallSummary()
    {
        $this->summary = [
            'total_batang' => 0,
            'total_lembar' => 0,
            'total_m3' => 0,
            'total_jam_kerja' => 0,
            'total_target' => 0,
            'total_status' => 0,
            'total_pot_target' => 0,
            'total_pekerja' => 0,
        ];

        foreach ($this->dataProduksi as $data) {
            $this->summary['total_batang'] += (int) ($data['summary']['total_batang'] ?? 0);
            $this->summary['total_lembar'] += (int) ($data['summary']['total_lembar'] ?? 0);
            $this->summary['total_m3'] += (float) ($data['summary']['total_m3'] ?? 0);
            $this->summary['total_jam_kerja'] += (int) ($data['summary']['jam_kerja'] ?? 0);

            // Hitung dari pekerja
            $pekerja = $data['pekerja'] ?? [];
            $this->summary['total_target'] += collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? 0));
            $this->summary['total_status'] += $data['summary']['total_lembar'] ?? 0; // Status = total lembar
            $this->summary['total_pot_target'] += collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? 0));
            $this->summary['total_pekerja'] += count($pekerja);
        }
    }

    public function exportToExcel()
    {
        $fileName = 'laporan-produksi-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new LaporanProduksiExport($this->dataProduksi),
            $fileName
        );
    }
}