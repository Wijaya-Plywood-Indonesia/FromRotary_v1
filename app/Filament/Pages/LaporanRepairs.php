<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Carbon\Carbon;

use BackedEnum;
use UnitEnum;

use Exception;
use Illuminate\Support\Facades\Log;

use App\Filament\Pages\LaporanRepairs\Queries\LoadLaporanRepairs;
use App\Filament\Pages\LaporanRepairs\Transformers\RepairDataMap;

class LaporanRepairs extends Page
{
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Laporan Repairs';
    protected string $view = 'filament.pages.laporan-repairs';

    /** FORM DATA */
    public array $data = [
        'tanggal' => null,
    ];

    /** DATA YANG AKAN DIKIRIM KE BLADE */
    public array $laporan = [];

    /** DATA PRODUKSI RAW */
    public array $dataProduksi = [];

    public bool $isLoading = false;

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
                    ->label('Tanggal')
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('d/m/Y')
                    ->live()
                    ->closeOnDateSelection()
                    ->afterStateUpdated(fn($state) => $this->onTanggalUpdated($state))
                    ->required()
                    ->maxDate(now())
                    ->default(now())
                    ->suffixIcon('heroicon-o-calendar')
                    ->suffixIconColor('primary'),
            ])
            ->statePath('data');
    }

    public function onTanggalUpdated($state): void
    {
        try {
            if ($state instanceof Carbon) {
                $tanggal = $state->format('Y-m-d');
            } elseif (is_string($state)) {
                $tanggal = Carbon::parse($state)->format('Y-m-d');
            } else {
                $tanggal = now()->format('Y-m-d');
            }

            $this->data['tanggal'] = $tanggal;

            $this->loadData();

        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Format Tanggal Tidak Valid')
                ->body('Silakan pilih tanggal yang valid.')
                ->send();

            $this->data['tanggal'] = now()->format('Y-m-d');
            $this->form->fill($this->data);
        }
    }

    /** LOAD DATA */
    public function loadData(): void
    {
        try {
            $this->isLoading = true;

            $tanggal = $this->data['tanggal'] ?? now()->format('Y-m-d');

            Log::info('Loading repair data for date: ' . $tanggal);

            $this->dataProduksi = [];

            $raw = LoadLaporanRepairs::run($tanggal);

            Log::info('Found ' . $raw->count() . ' repair records');

            $this->dataProduksi = RepairDataMap::make($raw);

            // === BAGIAN PENTING ===
            $this->laporan = $this->dataProduksi;

            if (empty($this->dataProduksi)) {
                Notification::make()
                    ->warning()
                    ->title('Tidak Ada Data')
                    ->body('Tidak ditemukan data repair untuk tanggal ' . Carbon::parse($tanggal)->format('d/m/Y'))
                    ->send();
            }

        } catch (Exception $e) {

            Notification::make()
                ->danger()
                ->title('Error Memuat Data')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->send();

            Log::error('Error loading repair data: ' . $e->getMessage());

            $this->dataProduksi = [];
            $this->laporan = [];

        } finally {
            $this->isLoading = false;
        }
    }

    /** KIRIM DATA KE BLADE */
    public function getViewData(): array
    {
        return [
            'laporan' => $this->laporan,
        ];
    }

    public function refresh(): void
    {
        $this->loadData();

        Notification::make()
            ->success()
            ->title('Data Diperbarui')
            ->body('Data berhasil dimuat ulang')
            ->send();
    }
}
