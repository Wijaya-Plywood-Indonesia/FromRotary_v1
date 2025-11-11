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
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;

class LaporanProduksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.laporan-produksi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Rotary';

    public $dataProduksi = [];
    public $tanggal = null;

    public bool $isLoading = false;

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->form->fill(['tanggal' => $this->tanggal]); // PAKAI form() dari HasForms
        $this->loadAllData();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('tanggal')
                ->label('Pilih Tanggal')
                ->default(now())
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->tanggal = $state;
                    $this->loadAllData();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportToExcel'),
        ];
    }

    // Fungsi Pembulatan
    protected function roundToNearestHundred(float $number): int
    {
        $thousands = floor($number / 1000);
        $base = $thousands * 1000;
        $remainder = $number - $base;

        if ($remainder < 300) {
            return $base;
        } elseif ($remainder < 800) {
            return $base + 500;
        } else {
            return $base + 1000;
        }
    }

    public function loadAllData()
    {
        $this->isLoading = true;

        $tanggal = $this->tanggal ?? now()->format('Y-m-d');

        $produksiList = ProduksiRotary::with([
            'mesin',
            'detailPegawaiRotary.pegawai',
            'detailPaletRotary',
        ])
            ->whereHas('detailPaletRotary')
            ->whereDate('tgl_produksi', $tanggal)
            ->orderBy('tgl_produksi', 'desc')
            ->get();

        $this->dataProduksi = [];

        foreach ($produksiList as $produksi) {
            $mesinNama = $produksi->mesin->nama_mesin;
            $tanggalFormat = \Carbon\Carbon::parse($produksi->tgl_produksi)->format('d/m/Y');
            $idUkuran = $produksi->detailPaletRotary->first()?->id_ukuran ?? 'TIDAK ADA UKURAN';

            $targetHarian = $produksi->detailPaletRotary->sum('total_lembar') ?? 0;

            $targetModel = \App\Models\Target::where('id_mesin', $produksi->id_mesin)
                ->where('id_ukuran', $idUkuran)
                ->first();

            $target = $targetModel?->target ?? 0;
            $jamKerja = $targetModel?->jam ?? 0;
            $targetPerJam = $jamKerja > 0 ? $target / $jamKerja : 0;

            $selisih = $targetHarian - $target;

            $jumlahPekerja = $produksi->detailPegawaiRotary->count();

            $potonganPerLembar = $targetModel?->potongan ?? 0;
            $potonganPerLembar = ceil($potonganPerLembar);
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($selisih < 0) {
                $potonganTotal = abs($selisih) * $potonganPerLembar;

                $potonganPerOrang = $jumlahPekerja > 0 ? $potonganTotal / $jumlahPekerja : 0;
            }

            $pekerja = [];
            foreach ($produksi->detailPegawaiRotary as $detail) {
                $pekerja[] = [
                    'id' => $detail->pegawai->kode_pegawai ?? '-',
                    'nama' => $detail->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $detail->jam_masuk ? \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') : '-',
                    'jam_pulang' => $detail->jam_pulang ? \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') : '-',
                    'ijin' => $detail->ijin ?? '-',
                    'pot_target' => $potonganPerOrang > 0
                        ? number_format($this->roundToNearestHundred($potonganPerOrang), 0, '', '.')
                        : '-',
                    'selisih' => $selisih,
                    'keterangan' => $detail->keterangan ?? '-',
                ];
            }

            $this->dataProduksi[] = [
                'tanggal' => $tanggalFormat,
                'mesin' => $mesinNama,
                'kode_ukuran' => $idUkuran,
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

        $this->isLoading = false;
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
        $tanggal = $this->tanggal ?? now()->format('Y-m-d');
        $fileName = 'laporan-produksi-' . \Carbon\Carbon::parse($tanggal)->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanProduksiExport($this->dataProduksi), $fileName);
    }
}