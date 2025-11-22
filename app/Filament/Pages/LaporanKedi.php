<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\DatePicker;

// Import Model yang Dibutuhkan
use App\Models\ProduksiKedi;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use BackedEnum;
use UnitEnum;


class LaporanKedi extends Page
{
    use InteractsWithForms;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.laporan-kedi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Kedi';
    protected static ?int $navigationSort = 1;

    public $dataKedi = []; // Mengganti dataStik menjadi dataKedi
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
                ->suffixIconColor('primary')
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
                ->label('Download Excel (WIP)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->disabled()
                ->disabled() // Menonaktifkan dulu karena Export Class belum dibuat
                ->action('exportToExcel'),
        ];
    }

    public function loadAllData()
    {
        $this->isLoading = true;
        $tanggal = $this->tanggal ?? now()->format('Y-m-d');

        // Ambil Data Produksi Kedi berdasarkan tanggal
        // Eager load semua relasi yang dibutuhkan: Detail Masuk, Detail Bongkar, Ukuran, Jenis Kayu
        $produksiList = ProduksiKedi::with([
                'detailMasukKedi.ukuran', 
                'detailMasukKedi.jenisKayu', 
                'detailBongkarKedi.ukuran', 
                'detailBongkarKedi.jenisKayu'
            ])
            ->whereDate('tanggal', $tanggal) 
            ->get();

        $this->dataKedi = [];

        foreach ($produksiList as $produksi) {
            
            // 1. Data Detail Masuk
            $detailMasuk = $produksi->detailMasukKedi->map(function ($detail) {
                return [
                    'no_palet' => $detail->no_palet,
                    // Menggunakan null-safe operator (?->)
                    'ukuran' => $detail->ukuran?->nama_ukuran ?? '-',
                    'jenis_kayu' => $detail->jenisKayu?->nama_kayu ?? '-',
                    'kw' => $detail->kw,
                    'jumlah' => $detail->jumlah,
                    'rencana_bongkar' => $detail->rencana_bongkar ? Carbon::parse($detail->rencana_bongkar)->format('d/m/Y') : '-',
                ];
            })->toArray();

            // 2. Data Detail Bongkar (Asumsi modelnya DetailBongkarKedi)
            $detailBongkar = $produksi->detailBongkarKedi->map(function ($detail) {
                return [
                    // Asumsi DetailBongkarKedi punya kolom yang sama atau setidaknya no_palet
                    'no_palet' => $detail->no_palet, 
                    'ukuran' => $detail->ukuran?->nama_ukuran ?? '-',
                    'jenis_kayu' => $detail->jenisKayu?->nama_kayu ?? '-',
                    'kw' => $detail->kw,
                    'jumlah' => $detail->jumlah,
                    'tanggal_bongkar' => $detail->tanggal_bongkar ? Carbon::parse($detail->tanggal_bongkar)->format('d/m/Y') : '-',
                    // Tambahkan kolom lain dari detail bongkar jika ada (misal: 'hasil_bongkar')
                    'hasil_bongkar' => $detail->hasil_bongkar ?? '-',
                ];
            })->toArray();


            $this->dataKedi[] = [
                'id_produksi' => $produksi->id,
                'tanggal_produksi' => Carbon::parse($produksi->tanggal_produksi)->format('d/m/Y'),
                'status' => $produksi->status ?? 'Belum diupdate', // Asumsi ada kolom 'status'
                'detail_masuk' => $detailMasuk,
                'detail_bongkar' => $detailBongkar,
            ];
        }

        $this->isLoading = false;
    }

    // public function exportToExcel() { ... } // Implementasi Export Class jika sudah dibuat
}