<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ProduksiRotary;
use Illuminate\Support\Facades\Response;

class LaporanProduksi extends Page
{
    /** Navigasi dan metadata halaman */
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Produksi';
    protected static string|null $navigationGroup = 'Laporan';
    protected static ?string $slug = 'laporan-produksi';
    protected static ?string $title = 'Laporan Produksi';
    protected static string $view = 'filament.pages.laporan-produksi';

    /** Data untuk tabel */
    public $produksi;

    public function mount(): void
    {
        $this->produksi = ProduksiRotary::with('mesin')->latest()->get();
    }

    /** Download semua data sebagai CSV */
    public function downloadLaporan()
    {
        $filename = 'laporan-produksi-' . now()->format('Y-m-d') . '.csv';
        $csv = "Tanggal Produksi,Mesin,Kendala\n";

        foreach ($this->produksi as $item) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\"\n",
                $item->tgl_produksi,
                $item->mesin->nama_mesin ?? '-',
                $item->kendala ?? '-'
            );
        }

        return Response::streamDownload(fn() => print ($csv), $filename);
    }

    /** Download satu baris data */
    public function downloadItem(int $id)
    {
        $item = ProduksiRotary::with('mesin')->findOrFail($id);
        $filename = 'laporan-produksi-' . $item->id . '.csv';

        $csv = "Tanggal Produksi,Mesin,Kendala\n";
        $csv .= sprintf(
            "\"%s\",\"%s\",\"%s\"\n",
            $item->tgl_produksi,
            $item->mesin->nama_mesin ?? '-',
            $item->kendala ?? '-'
        );

        return Response::streamDownload(fn() => print ($csv), $filename);
    }
}
