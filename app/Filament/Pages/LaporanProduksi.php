<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ProduksiRotary;
use App\Models\Target;
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
            'detailPaletRotary',
        ])
            ->whereHas('detailPaletRotary')
            ->orderBy('tgl_produksi', 'desc')
            ->limit(10)
            ->get();

        $this->dataProduksi = [];

        foreach ($produksiList as $produksi) {
            $mesinNama = $produksi->mesin->nama_mesin;
            $tanggal = \Carbon\Carbon::parse($produksi->tgl_produksi)->format('d/m/Y');

            $targetHarian = $produksi->detailPaletRotary->sum('total_lembar') ?? 0;

            // --- CARI TARGET BERDASARKAN kode_ukuran ---
            $idUkuran = $produksi->detailPaletRotary->first()?->id_ukuran ?? 0;

            // CARI TARGET BERDASARKAN id_mesin + id_ukuran
            $targetModel = \App\Models\Target::where('id_mesin', $produksi->id_mesin)
                ->where('id_ukuran', $idUkuran)
                ->first();

            // --- VARIABEL LAMA TETAP DIPAKAI ---
            $target = $targetModel?->target ?? 0;
            $jamKerja = $targetModel?->jam ?? 0;
            $targetPerJam = $jamKerja > 0 ? $target / $jamKerja : 0;

            // SELISIH
            $selisih = $targetHarian - $target;

            // --- HITUNG JUMLAH PEKERJA ---
            $jumlahPekerja = $produksi->detailPegawaiRotary->count();

            // --- AMBIL POTONGAN PER LEMBAR DARI TARGET ---
            $potonganPerLembar = $targetModel?->potongan ?? 0;

            // --- HITUNG POTONGAN (HANYA JIKA KURANG) ---
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($selisih < 0) {
                $potonganTotal = ceil(abs($selisih) * $potonganPerLembar);
                $potonganPerOrang = $jumlahPekerja > 0 ? $potonganTotal / $jumlahPekerja : 0;
            }

            // --- DATA PEKERJA ---
            $pekerja = [];
            foreach ($produksi->detailPegawaiRotary as $detail) {
                $pekerja[] = [
                    'id' => $detail->pegawai->kode_pegawai ?? '-',
                    'nama' => $detail->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') : '-',
                    'jam_pulang' => $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') : '-',
                    'ijin' => $detail->ijin ?? '-',
                    'pot_target' => $potonganPerOrang > 0 ? number_format(round($potonganPerOrang, 2), 0, '', '.') : '-',
                    'selisih' => $selisih,
                    'keterangan' => $detail->keterangan ?? '-',
                ];
            }

            $this->dataProduksi[] = [
                'tanggal' => $tanggal,
                'mesin' => $mesinNama,
                'pekerja' => $pekerja,
                'kendala' => $produksi->kendala ?? 'Tidak ada kendala.',
                'total_target_harian' => $targetHarian,
                'target' => $target,
                'target_per_jam' => $targetPerJam,
                'jam_kerja' => $jamKerja,
                'selisih' => $selisih,
                'summary' => [
                    'jumlah_pekerja' => count($pekerja),
                    'total_target_harian' => $target,
                    'total_status_harian' => $targetHarian,
                ]
            ];
        }

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
            'total_hasil_produksi' => 0,
        ];

        foreach ($this->dataProduksi as $data) {
            $this->summary['total_hasil_produksi'] += $data['total_target_harian'];
            $this->summary['total_target'] += $data['target'];
            $this->summary['total_pekerja'] += $data['summary']['jumlah_pekerja'];

            $pekerja = $data['pekerja'] ?? [];
            $this->summary['total_pot_target'] += collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? 0));
        }
    }

    public function exportToExcel()
    {
        $fileName = 'laporan-produksi-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new LaporanProduksiExport($this->dataProduksi), $fileName);
    }
}