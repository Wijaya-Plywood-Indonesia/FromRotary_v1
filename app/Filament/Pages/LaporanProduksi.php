<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ProduksiRotary;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

use App\Exports\LaporanProduksiExport;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
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
            'detailPaletRotary.setoranPaletUkuran',
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

        // Group by ukuran dan KW
        foreach ($produksi->detailPaletRotary as $detail) {
            $ukuran = $detail->setoranPaletUkuran->ukuran ?? 'Unknown';
            $kw = $detail->kw ?? 'Unknown';

            if (!isset($hasil[$ukuran])) {
                $hasil[$ukuran] = [];
            }

            if (!isset($hasil[$ukuran][$kw])) {
                $hasil[$ukuran][$kw] = [
                    'palet' => 0,
                    'lembar' => 0,
                ];
            }

            $hasil[$ukuran][$kw]['palet'] += $detail->palet;
            $hasil[$ukuran][$kw]['lembar'] += $detail->total_lembar;
        }

        return $hasil;
    }

    protected function getPekerjaData($produksi)
    {
        $data = [];

        foreach ($produksi->detailPegawaiRotary as $detail) {
            $data[] = [
                'id' => $detail->id_pegawai ?? '-',
                'nama' => $detail->pegawai->nama ?? '-',
                'jam_masuk' => $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') : '-',
                'jam_pulang' => $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') : '-',
                'ijin' => $detail->ijin ?? '-',
                'pot_target' => $detail->pot_target ?? '-',
                'keterangan' => $detail->keterangan ?? '-',
            ];
        }

        return $data;
    }

    protected function calculateSummary($produksi)
    {
        $totalBatang = $produksi->detailLahanRotary->sum('jumlah_batang');
        $totalPalet = $produksi->detailPaletRotary->sum('palet');
        $totalLembar = $produksi->detailPaletRotary->sum('total_lembar');
        $totalM3 = $totalLembar * 0.001; // Konversi ke m3

        // Hitung jam kerja (asumsi dari pekerja pertama)
        $pekerjaFirst = $produksi->detailPegawaiRotary->first();
        $jamKerja = 0;
        if ($pekerjaFirst && $pekerjaFirst->jam_masuk && $pekerjaFirst->jam_pulang) {
            $masuk = \Carbon\Carbon::parse($pekerjaFirst->jam_masuk);
            $pulang = \Carbon\Carbon::parse($pekerjaFirst->jam_pulang);
            $jamKerja = $pulang->diffInHours($masuk);
        }

        return [
            'total_batang' => $totalBatang,
            'total_palet' => $totalPalet,
            'total_lembar' => $totalLembar,
            'total_m3' => $totalM3,
            'jam_kerja' => $jamKerja,
            'jumlah_pekerja' => $produksi->detailPegawaiRotary->count(),
        ];
    }

    protected function calculateOverallSummary()
    {
        $this->summary = [
            'total_batang' => 0,
            'total_palet' => 0,
            'total_lembar' => 0,
            'total_m3' => 0,
            'total_pekerja' => 0,
        ];

        foreach ($this->dataProduksi as $data) {
            $this->summary['total_batang'] += $data['summary']['total_batang'];
            $this->summary['total_palet'] += $data['summary']['total_palet'];
            $this->summary['total_lembar'] += $data['summary']['total_lembar'];
            $this->summary['total_m3'] += $data['summary']['total_m3'];
            $this->summary['total_pekerja'] += $data['summary']['jumlah_pekerja'];
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