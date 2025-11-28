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

            Action::make('laporan_kayu')
                ->label('Laporan Kayu Masuk')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(route('laporan.kayu-masuk'))
                ->openUrlInNewTab(),



        ];
    }
}
