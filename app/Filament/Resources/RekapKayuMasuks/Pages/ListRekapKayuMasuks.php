<?php

namespace App\Filament\Resources\RekapKayuMasuks\Pages;

use App\Filament\Resources\RekapKayuMasuks\RekapKayuMasukResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKayu;
use App\Models\RekapKayuMasuk;

class ListRekapKayuMasuks extends ListRecords
{
    protected static string $resource = RekapKayuMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // ========================
            // EXPORT EXCEL
            // ========================
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {

                    // Query untuk export
                    $query = RekapKayuMasuk::query()->with([
                        'kayuMasuk.penggunaanSupplier',
                        'jenisKayu',
                        'lahan',
                    ]);

                    // Kolom export (Sesuai kebutuhan kamu)
                    $columns = [
                        ['label' => 'Tanggal', 'field' => 'tanggal'], // accessor
                        ['label' => 'Nama', 'field' => 'nama'],       // accessor
                        ['label' => 'Seri', 'field' => 'seri'],
                        ['label' => 'Panjang', 'field' => 'panjang'],
                        ['label' => 'Jenis', 'field' => 'jenisKayu.nama_jenis'],
                        ['label' => 'Lahan', 'field' => 'lahan.kode_lahan'],
                        ['label' => 'Banyak', 'field' => 'kuantitas'],
                        ['label' => 'M3', 'field' => 'kubikasi'],    // accessor
                        ['label' => 'Poin', 'field' => 'total_harga'], // accessor
                    ];

                    return Excel::download(
                        new LaporanKayu($query, $columns),
                        'rekap-detail-kayu.xlsx'
                    );
                }),


            // ========================
            // EXPORT CSV
            // ========================
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-document-text')
                ->action(function () {

                    $query = RekapKayuMasuk::query()->with([
                        'kayuMasuk.penggunaanSupplier',
                        'jenisKayu',
                        'lahan',
                    ]);

                    $columns = [
                        ['label' => 'Tanggal', 'field' => 'tanggal'],
                        ['label' => 'Nama', 'field' => 'nama'],
                        ['label' => 'Seri', 'field' => 'seri'],
                        ['label' => 'Panjang', 'field' => 'panjang'],
                        ['label' => 'Jenis', 'field' => 'jenisKayu.nama_jenis'],
                        ['label' => 'Lahan', 'field' => 'lahan.kode_lahan'],
                        ['label' => 'Banyak', 'field' => 'kuantitas'],
                        ['label' => 'M3', 'field' => 'kubikasi'],
                        ['label' => 'Poin', 'field' => 'total_harga'],
                    ];

                    return Excel::download(
                        new LaporanKayu($query, $columns),
                        'rekap-detail-kayu.csv'
                    );
                }),

        ];
    }
}
