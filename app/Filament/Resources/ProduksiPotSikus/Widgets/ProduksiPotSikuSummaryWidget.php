<?php

namespace App\Filament\Resources\ProduksiPotSikus\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use App\Models\ProduksiPotSiku;
use App\Models\DetailBarangDikerjakanPotSiku;

class ProduksiPotSikuSummaryWidget extends Widget
{
    protected string $view = 'filament.resources.produksi-pot-siku.widgets.summary';

    protected int|string|array $columnSpan = 'full';

    public ?ProduksiPotSiku $record = null;

    public array $summary = [];

    public function mount(?ProduksiPotSiku $record = null): void
    {
        if (! $record) {
            return;
        }

        $produksiId = $record->id;

        // ======================
        // TOTAL KESELURUHAN
        // ======================
        $totalAll = DetailBarangDikerjakanPotSiku::where('id_produksi_pot_siku', $produksiId)
            ->sum(DB::raw('CAST(jumlah AS UNSIGNED)'));

        // ======================
        // GLOBAL UKURAN + KW
        // ======================
        $globalUkuranKw = DetailBarangDikerjakanPotSiku::query()
            ->where('id_produksi_pot_siku', $produksiId)
            ->join('ukurans', 'ukurans.id', '=', 'detail_barang_dikerjakan_pot_siku.id_ukuran')
            ->selectRaw('
                CONCAT(
                    TRIM(TRAILING ".00" FROM CAST(ukurans.panjang AS CHAR)), " x ",
                    TRIM(TRAILING ".00" FROM CAST(ukurans.lebar AS CHAR)), " x ",
                    TRIM(TRAILING "0" FROM TRIM(TRAILING "." FROM CAST(ukurans.tebal AS CHAR)))
                ) AS ukuran,
                detail_barang_dikerjakan_pot_siku.kw,
                SUM(CAST(detail_barang_dikerjakan_pot_siku.jumlah AS UNSIGNED)) AS total
            ')
            ->groupBy('ukuran', 'detail_barang_dikerjakan_pot_siku.kw')
            ->orderBy('ukuran')
            ->orderBy('detail_barang_dikerjakan_pot_siku.kw')
            ->get();

        $globalUkuran = DetailBarangDikerjakanPotSiku::query()
            ->where('id_produksi_pot_siku', $produksiId)
            ->join('ukurans', 'ukurans.id', '=', 'detail_barang_dikerjakan_pot_siku.id_ukuran')
            ->selectRaw('
                CONCAT(
                    TRIM(TRAILING ".00" FROM CAST(ukurans.panjang AS CHAR)), " x ",
                    TRIM(TRAILING ".00" FROM CAST(ukurans.lebar AS CHAR)), " x ",
                    TRIM(TRAILING "0" FROM TRIM(TRAILING "." FROM CAST(ukurans.tebal AS CHAR)))
                ) AS ukuran,
                SUM(CAST(detail_barang_dikerjakan_pot_siku.jumlah AS UNSIGNED)) AS total
            ')
            ->groupBy('ukuran')
            ->orderBy('ukuran')
            ->get();

        $this->summary = [
            'totalAll'       => $totalAll,
            'globalUkuranKw' => $globalUkuranKw,
            'globalUkuran'   => $globalUkuran,
        ];
    }
}
