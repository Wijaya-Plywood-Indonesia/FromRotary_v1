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

// Import Query Classes
use App\Filament\Pages\LaporanProduksi\Queries\LoadProduksi as QueryRotary;
use App\Filament\Pages\LaporanRepairs\Queries\LoadLaporanRepairs as QueryRepair;
use App\Filament\Pages\LaporanDryer\Queries\LoadLaporanDryer as QueryDryer;
use App\Filament\Pages\LaporanKedi\Queries\LoadLaporanKedi as QueryKedi;
use App\Filament\Pages\LaporanStik\Queries\LoadLaporanStik as QueryStik;

// Import Transformer Classes
use App\Filament\Pages\LaporanHarian\Transformers\RotaryWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\RepairWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\DryerWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\KediWorkerMap;
use App\Filament\Pages\LaporanHarian\Transformers\StikWorkerMap;

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

    // Statistics untuk debugging
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
                    ->helperText('Pilih tanggal untuk memuat gabungan data semua divisi.'),
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
        $tgl = $this->data['tanggal'] ?? now()->format('Y-m-d');

        try {
            Log::info('LaporanHarian: Memuat data untuk tanggal ' . $tgl);

            // Reset statistics
            $this->statistics = array_fill_keys(['rotary', 'repair', 'dryer', 'kedi', 'stik', 'total'], 0);

            $merged = [];

            // 1. ROTARY
            try {
                $rawRotary = QueryRotary::run($tgl);
                $listRotary = RotaryWorkerMap::make($rawRotary);
                $this->statistics['rotary'] = count($listRotary);
                $merged = array_merge($merged, $listRotary);
                Log::info("Rotary: {$this->statistics['rotary']} pegawai");
            } catch (Exception $e) {
                Log::error("Gagal memuat Rotary: " . $e->getMessage());
            }

            // 2. REPAIR
            try {
                $rawRepair = QueryRepair::run($tgl);
                $listRepair = RepairWorkerMap::make($rawRepair);
                $this->statistics['repair'] = count($listRepair);
                $merged = array_merge($merged, $listRepair);
                Log::info("Repair: {$this->statistics['repair']} pegawai");
            } catch (Exception $e) {
                Log::error("Gagal memuat Repair: " . $e->getMessage());
            }

            // 3. DRYER (jika sudah ada Query & Transformer)
            try {
                if (class_exists(QueryDryer::class) && class_exists(DryerWorkerMap::class)) {
                    $rawDryer = QueryDryer::run($tgl);
                    $listDryer = DryerWorkerMap::make($rawDryer);
                    $this->statistics['dryer'] = count($listDryer);
                    $merged = array_merge($merged, $listDryer);
                    Log::info("Dryer: {$this->statistics['dryer']} pegawai");
                }
            } catch (Exception $e) {
                Log::error("Gagal memuat Dryer: " . $e->getMessage());
            }

            // 4. KEDI (jika sudah ada Query & Transformer)
            try {
                if (class_exists(QueryKedi::class) && class_exists(KediWorkerMap::class)) {
                    $rawKedi = QueryKedi::run($tgl);
                    $listKedi = KediWorkerMap::make($rawKedi);
                    $this->statistics['kedi'] = count($listKedi);
                    $merged = array_merge($merged, $listKedi);
                    Log::info("Kedi: {$this->statistics['kedi']} pegawai");
                }
            } catch (Exception $e) {
                Log::error("Gagal memuat Kedi: " . $e->getMessage());
            }

            // 5. STIK (jika sudah ada Query & Transformer)
            try {
                if (class_exists(QueryStik::class) && class_exists(StikWorkerMap::class)) {
                    $rawStik = QueryStik::run($tgl);
                    $listStik = StikWorkerMap::make($rawStik);
                    $this->statistics['stik'] = count($listStik);
                    $merged = array_merge($merged, $listStik);
                    Log::info("Stik: {$this->statistics['stik']} pegawai");
                }
            } catch (Exception $e) {
                Log::error("Gagal memuat Stik: " . $e->getMessage());
            }

            // Sort berdasarkan nama pegawai
            usort($merged, function ($a, $b) {
                return strcmp($a['nama'] ?? '', $b['nama'] ?? '');
            });

            $this->laporanGabungan = $merged;
            $this->statistics['total'] = count($merged);

            Log::info('LaporanHarian: Total ' . $this->statistics['total'] . ' pegawai dimuat');

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
                    ->body("Total {$this->statistics['total']} pegawai dari semua divisi")
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