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
            'detailPegawaiRotary.pegawai',
        ])
            ->orderBy('tgl_produksi', 'desc')
            ->limit(10)
            ->get();

        $this->dataProduksi = [];

        foreach ($produksiList as $produksi) {
            $mesinNama = $produksi->mesin->nama_mesin ?? $produksi->id_mesin;
            $tanggal = \Carbon\Carbon::parse($produksi->tgl_produksi)->format('d/m/Y');

            // HANYA AMBIL DATA PEKERJA
            $pekerja = [];
            foreach ($produksi->detailPegawaiRotary as $detail) {
                $pekerja[] = [
                    'id' => $detail->pegawai->kode_pegawai ?? '-',
                    'nama' => $detail->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') : '-',
                    'jam_pulang' => $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') : '-',
                    'ijin' => $detail->ijin ?? '-',
                    'pot_target' => number_format((float) str_replace('.', '', $detail->pot_target ?? 0), 0, '', '.'),
                    'keterangan' => $detail->keterangan ?? '-',
                ];
            }

            // Summary harian pekerja
            $totalTargetHarian = collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? 0));

            $this->dataProduksi[] = [
                'tanggal' => $tanggal,
                'mesin' => $mesinNama,
                'bahan' => [], // Kosong
                'hasil' => [], // Kosong
                'pekerja' => $pekerja,
                'kendala' => $produksi->kendala ?? 'Tidak ada kendala.',
                'summary' => [
                    'total_batang' => 0,
                    'total_lembar' => 0,
                    'total_m3' => 0,
                    'jam_kerja' => 0,
                    'jumlah_pekerja' => count($pekerja),
                    'total_target_harian' => $totalTargetHarian,
                    'total_status_harian' => 0,
                ],
            ];
        }

        // Hitung summary keseluruhan (tetap pakai fungsi Anda)
        $this->calculateOverallSummary();
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
        return Excel::download(new LaporanProduksiExport($this->dataProduksi), $fileName);
    }
}