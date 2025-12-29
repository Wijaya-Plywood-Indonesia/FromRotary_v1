<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;
use BackedEnum;

// --- 1. IMPORT QUERY CLASSES ---
use App\Filament\Pages\LaporanProduksi\Queries\LoadProduksi as QueryRotary;
use App\Filament\Pages\LaporanRepairs\Queries\LoadLaporanRepairs as QueryRepair;
use App\Filament\Pages\LaporanPressDryer\Queries\LoadPressDryer as QueryDryer; // Namespace Dryer
use App\Filament\Pages\LaporanProduksi\Queries\LoadProduksiStik as QueryStik;   // Namespace Stik
// use App\Filament\Pages\LaporanKedi\Queries\LoadProduksiKedi as QueryKedi;    // (UNCOMMENT JIKA SUDAH ADA FILE QUERY KEDI)

// --- 2. IMPORT TRANSFORMER CLASSES ---
use App\Filament\Pages\LaporanHarian\Transformers\RotaryWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\RepairWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\PressDryerWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\StikWorkerMap;
// use App\Filament\Pages\LaporanHarian\Transformers\KediWorkerMap;

use App\Exports\LaporanHarianExport;

class LaporanHarian extends Page implements HasForms
{
    use InteractsWithForms;

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Laporan Harian';

    protected string $view = 'filament.pages.laporan-harian';

    public ?array $data = [
        'tanggal' => null,
    ];

    public array $laporanGabungan = [];
    public bool $isLoading = false;

    // Statistics
    public array $statistics = [
        'rotary' => 0,
        'repair' => 0,
        'dryer' => 0,
        'kedi' => 0,
        'stik' => 0,
        'total' => 0,
    ];

    public function mount(): void
    {
        $this->data['tanggal'] = now()->format('Y-m-d');
        $this->form->fill($this->data);
        $this->loadData();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('tanggal')
                    ->label('Pilih Tanggal Laporan')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->default(now())
                    ->live()
                    ->closeOnDateSelection()
                    ->afterStateUpdated(fn() => $this->loadData())
                    ->suffixIcon('heroicon-o-calendar')
                    ->suffixIconColor('primary')
                    ->helperText('Pilih tanggal untuk memuat gabungan data Rotary, Repair, Dryer, Stik, dll.'),
            ])
            ->statePath('data')
            ->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn() => $this->loadData()),

            Action::make('export')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportExcel())
                ->visible(fn() => !empty($this->laporanGabungan)),
        ];
    }

    public function loadData(): void
    {
        $this->isLoading = true;

        // Pastikan format tanggal bersih (Y-m-d)
        $rawTgl = $this->data['tanggal'] ?? now();
        $tgl = Carbon::parse($rawTgl)->format('Y-m-d');

        try {
            Log::info('LaporanHarian: Memuat data untuk tanggal ' . $tgl);

            // Reset statistics
            $this->statistics = [
                'rotary' => 0,
                'repair' => 0,
                'dryer' => 0,
                'kedi' => 0,
                'stik' => 0,
                'total' => 0,
            ];

            $listRotary = [];
            $listRepair = [];
            $listDryer = [];
            $listStik = [];
            $listKedi = [];

            // ===========================================
            // 1. ROTARY
            // ===========================================
            try {
                $rawRotary = QueryRotary::run($tgl);
                $listRotary = RotaryWorkerMap::make($rawRotary);
                $this->statistics['rotary'] = count($listRotary);
            } catch (Exception $e) {
                Log::error("❌ Gagal memuat Rotary: " . $e->getMessage());
            }

            // ===========================================
            // 2. REPAIR
            // ===========================================
            try {
                $rawRepair = QueryRepair::run($tgl);
                $listRepair = RepairWorkerMap::make($rawRepair);
                $this->statistics['repair'] = count($listRepair);
            } catch (Exception $e) {
                Log::error("❌ Gagal memuat Repair: " . $e->getMessage());
            }

            // ===========================================
            // 3. PRESS DRYER
            // ===========================================
            try {
                $rawDryer = QueryDryer::run($tgl);
                $listDryer = PressDryerWorkerMap::make($rawDryer);
                $this->statistics['dryer'] = count($listDryer);
            } catch (Exception $e) {
                Log::error("❌ Gagal memuat Dryer: " . $e->getMessage());
            }

            // ===========================================
            // 4. STIK
            // ===========================================
            try {
                $rawStik = QueryStik::run($tgl);
                $listStik = StikWorkerMap::make($rawStik);
                $this->statistics['stik'] = count($listStik);
            } catch (Exception $e) {
                Log::error("❌ Gagal memuat Stik: " . $e->getMessage());
            }

            // ===========================================
            // 5. KEDI (Placeholder - Uncomment jika Query Ready)
            // ===========================================
            /*
            try {
                $rawKedi = QueryKedi::run($tgl); // Pastikan class QueryKedi sudah di-import
                $listKedi = KediWorkerMap::make($rawKedi);
                $this->statistics['kedi'] = count($listKedi);
            } catch (Exception $e) {
                Log::error("❌ Gagal memuat Kedi: " . $e->getMessage());
            }
            */

            // ===========================================
            // MERGE & SORT
            // ===========================================
            $merged = array_merge(
                $listRotary,
                $listRepair,
                $listDryer,
                $listStik,
                $listKedi
            );

            // Sort berdasarkan nama pegawai A-Z
            usort($merged, function ($a, $b) {
                return strcmp($a['nama'] ?? '', $b['nama'] ?? '');
            });

            $this->laporanGabungan = $merged;
            $this->statistics['total'] = count($merged);

            // Notification
            if (empty($merged)) {
                Notification::make()
                    ->warning()
                    ->title('Data Kosong')
                    ->body('Tidak ditemukan data pegawai untuk tanggal ' . Carbon::parse($tgl)->format('d/m/Y'))
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title('Data Berhasil Dimuat')
                    ->body("Total {$this->statistics['total']} pegawai ditemukan.")
                    ->send();
            }

        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Terjadi Kesalahan')
                ->body($e->getMessage())
                ->send();

            Log::error('LaporanHarian Error: ' . $e->getMessage());
            $this->laporanGabungan = [];
        } finally {
            $this->isLoading = false;
        }
    }

    public function exportExcel()
    {
        try {
            $tgl = $this->data['tanggal'];
            return Excel::download(
                new LaporanHarianExport($this->laporanGabungan),
                "Laporan-Harian-Gabungan-{$tgl}.xlsx"
            );
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Export')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getViewData(): array
    {
        return [
            'laporanGabungan' => $this->laporanGabungan,
            'isLoading' => $this->isLoading,
            'statistics' => $this->statistics,
        ];
    }
}