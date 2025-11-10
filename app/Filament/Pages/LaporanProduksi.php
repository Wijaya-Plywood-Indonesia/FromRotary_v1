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
use Filament\Schemas\Components\Grid;
use Maatwebsite\Excel\Facades\Excel;

class LaporanProduksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.laporan-produksi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Rotary';

    // TAMBAH INI → FIX ERROR $heading
    public function getHeading(): string
    {
        return static::$title;
    }

    public $dataProduksi = [];
    public $tanggal = null;

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->form->fill(['tanggal' => $this->tanggal]);
        $this->loadAllData();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->reactive()
                        ->afterStateUpdated(fn($state) => $this->tanggal = $state && $this->loadAllData())
                        ->columnSpan(1),
                ])
                ->columnSpan(1),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->button()
                ->size('sm')
                ->action('exportToExcel'),
        ];
    }

    public function loadAllData()
    {
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
            $kodeUkuran = $produksi->detailPaletRotary->first()?->kode_ukuran ?? 'TIDAK ADA UKURAN';

            $targetHarian = $produksi->detailPaletRotary->sum('total_lembar') ?? 0;

            $targetModel = \App\Models\Target::where('id_mesin', $produksi->id_mesin)
                ->where('kode_ukuran', $kodeUkuran)
                ->first();

            $target = $targetModel?->target ?? 0;
            $jamKerja = $targetModel?->jam ?? 0;
            $targetPerJam = $jamKerja > 0 ? $target / $jamKerja : 0;

            $selisih = $targetHarian - $target;
            $jumlahPekerja = $produksi->detailPegawaiRotary->count();

            $potonganPerLembar = $targetModel?->potongan_per_lembar ?? 0;
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            if ($selisih < 0) {
                $potonganTotal = ceil(abs($selisih) * $potonganPerLembar);
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
                    'pot_target' => $potonganPerOrang > 0 ? number_format(round($potonganPerOrang, 2), 0, '', '.') : '-',
                    'selisih' => $selisih,
                    'keterangan' => $detail->keterangan ?? '-',
                ];
            }

            $this->dataProduksi[] = [
                'tanggal' => $tanggalFormat,
                'mesin' => $mesinNama,
                'kode_ukuran' => $kodeUkuran,
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
            $this->summary['total,total_pekerja'] += $data['summary']['jumlah_pekerja'];

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