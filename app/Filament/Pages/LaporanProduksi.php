<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use App\Filament\Pages\LaporanProduksi\Queries\LoadProduksi;
use App\Filament\Pages\LaporanProduksi\Transformers\ProduksiDataMap;

use BackedEnum;
use UnitEnum;

class LaporanProduksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.laporan-produksi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Rotary';

    // Form state container (statePath => 'data')
    public array $data = [
        'tanggal' => null,
    ];

    public array $dataProduksi = [];
    public bool $isLoading = false;

    // Inisialisasi halaman
    public function mount(): void
    {
        // set default tanggal di state (YYYY-MM-DD)
        $this->data['tanggal'] = now()->format('Y-m-d');

        // Isi awal form (opsional; InteractsWithForms akan menampilkan form berdasarkan statePath)
        $this->form->fill($this->data);

        $this->loadData();
    }

    // DatePicker untuk memilih tanggal laporan
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->native(false)                      // modern, responsive
                    ->displayFormat('d/m/Y')             // tampil di UI
                    ->reactive()                         // realtime update
                    ->afterStateUpdated(fn($state) => $this->onTanggalUpdated($state))
                    ->required(),
            ])
            ->statePath('data'); // penting: menyambungkan schema -> $this->data
    }

    // handler ketika tanggal berubah
    public function onTanggalUpdated($state): void
    {
        // normalisasi format tanggal menjadi YYYY-MM-DD agar konsisten
        $this->data['tanggal'] = $state instanceof \Carbon\Carbon
            ? $state->format('Y-m-d')
            : date('Y-m-d', strtotime($state));

        $this->loadData();
    }

    // Load data produksi berdasarkan tanggal
    public function loadData(): void
    {


        $this->isLoading = true;

        $tanggal = $this->data['tanggal'] ?? now()->format('Y-m-d');

        // LoadProduksi::run harus mengembalikan koleksi Eloquent
        $raw = LoadProduksi::run($tanggal);

        $this->dataProduksi = ProduksiDataMap::make($raw);

        $this->isLoading = false;
    }
}
