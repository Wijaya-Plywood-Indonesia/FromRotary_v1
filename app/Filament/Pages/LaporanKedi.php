<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanProduksiKediExport;

use App\Models\ProduksiKedi;
use Filament\Actions\Action;
use Carbon\Carbon;

use BackedEnum;
use UnitEnum;

class LaporanKedi extends Page
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Kedi';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.laporan-kedi';

    public $dataKedi = [];
    public $tanggal = null;
    public bool $isLoading = false;

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->form->fill(['tanggal' => $this->tanggal]);
        $this->loadAllData();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('tanggal')
                ->label('Pilih Tanggal')
                ->reactive()
                ->format('Y-m-d')
                ->displayFormat('d/m/Y')
                ->live()
                ->required()
                ->maxDate(now())
                ->default(now())
                ->afterStateUpdated(function ($state) {
                    $this->tanggal = $state;
                    $this->loadAllData();
                }),
        ];
    }

    // --- METHOD UNTUK EKSPOR EXCEL BARU ---
    public function exportToExcel()
    {
        // 1. Validasi Data
        if (empty($this->dataKedi)) {
            Notification::make()
                ->title('Gagal Export')
                ->body('Tidak ada data Produksi Kedi yang tersedia pada tanggal tersebut untuk diekspor.')
                ->danger()
                ->send();
            return;
        }

        // 2. Tentukan nama file
        $tanggal = $this->tanggal ?? now()->format('Y-m-d');
        $filename = 'Laporan-Produksi-Kedi-' . Carbon::parse($tanggal)->format('Y-m-d') . '.xlsx';

        // 3. Panggil Maatwebsite\Excel
        // Menggunakan class export yang baru dibuat dan data yang sudah difilter/diproses
        return Excel::download(new LaporanProduksiKediExport($this->dataKedi), $filename);
    }

    // --- MENAMBAHKAN ACTION (TOMBOL EXPORT) ---
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export ke Excel')
                ->tooltip('Export data yang sudah difilter ke format Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportToExcel'),
        ];
    }

    public function loadAllData()
    {
        $this->isLoading = true;

        $tanggal = $this->tanggal ?? now()->format('Y-m-d');

        // Modifikasi Query: Menambahkan whereHas untuk memfilter STATUS VALIDASI TERAKHIR
        $produksiList = ProduksiKedi::with([
            'detailMasukKedi.ukuran',
            'detailMasukKedi.jenisKayu',
            'detailBongkarKedi.ukuran',
            'detailBongkarKedi.jenisKayu',
            'validasiTerakhir', // Eager load validasi terakhir
        ])
        ->whereDate('tanggal', $tanggal)
        
        // --- LOGIKA FILTER BARU: HANYA YANG SUDAH DIVALIDASI ('divalidasi' atau 'disetujui') ---
        ->whereHas('validasiTerakhir', function ($query) {
            $query->whereIn('status', ['divalidasi']);
        })
        // --------------------------------------------------------------------------------------
        
        ->get();

        $this->dataKedi = [];

        foreach ($produksiList as $produksi) {

            // Detail MASUK
            $detailMasuk = $produksi->detailMasukKedi->map(function ($d) {
                return [
                    'no_palet' => $d->no_palet,
                    'kode_kedi' => $d->kode_kedi,
                    'ukuran' => $d->ukuran?->nama_ukuran ?? '-',
                    'jenis_kayu' => $d->jenisKayu?->nama_kayu ?? '-',
                    'kw' => $d->kw,
                    'jumlah' => $d->jumlah,
                    'rencana_bongkar' => $d->rencana_bongkar
                        ? Carbon::parse($d->rencana_bongkar)->format('d/m/Y')
                        : '-',
                ];
            })->toArray();

            // Detail BONGKAR
            $detailBongkar = $produksi->detailBongkarKedi->map(function ($d) {
                return [
                    'no_palet' => $d->no_palet,
                    'kode_kedi' => $d->kode_kedi,
                    'ukuran' => $d->ukuran?->nama_ukuran ?? '-',
                    'jenis_kayu' => $d->jenisKayu?->nama_kayu ?? '-',
                    'kw' => $d->kw,
                    'jumlah' => $d->jumlah,
                    'tanggal_bongkar' => $d->tanggal_bongkar
                        ? Carbon::parse($d->tanggal_bongkar)->format('d/m/Y')
                        : '-',
                ];
            })->toArray();

            // Ambil kode kedi dari detail sesuai status
            $kodeKedi = null;
            if ($produksi->status === 'masuk') {
                $kodeKedi = optional($produksi->detailMasukKedi->first())->kode_kedi;
            } else {
                $kodeKedi = optional($produksi->detailBongkarKedi->first())->kode_kedi;
            }

            // FILTER per status
            $masuk = $produksi->status === 'masuk' ? $detailMasuk : [];
            $bongkar = $produksi->status === 'bongkar' ? $detailBongkar : [];

            $this->dataKedi[] = [
                'id_produksi' => $produksi->id,
                'kode_kedi' => $kodeKedi,
                'tanggal_produksi' => Carbon::parse($produksi->tanggal)->format('d/m/Y'),
                'status' => $produksi->status,
                'detail_masuk' => $masuk,
                'detail_bongkar' => $bongkar,
                'validasi_terakhir' => $produksi->validasiTerakhir?->status ?? 'Belum Ada',
                'validasi_oleh' => $produksi->validasiTerakhir?->role ?? '-',
            ];
        }

        $this->isLoading = false;
    }
}