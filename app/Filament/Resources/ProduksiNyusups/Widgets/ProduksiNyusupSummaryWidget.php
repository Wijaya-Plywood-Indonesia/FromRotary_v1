<?php

namespace App\Filament\Resources\ProduksiNyusups\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use App\Models\ProduksiNyusup;
use App\Models\DetailBarangDikerjakan;

class ProduksiNyusupSummaryWidget extends Widget
{
    protected string $view = 'filament.resources.produksi-nyusups.widget.summary';

    protected int|string|array $columnSpan = 'full';

    public ?ProduksiNyusup $record = null;

    public array $summary = [];

    public function mount(?ProduksiNyusup $record = null): void
    {
        if (! $record) {
            return;
        }

        $produksiId = $record->id;

        // ======================
        // TOTAL KESELURUHAN
        // ======================
        $totalAll = DetailBarangDikerjakan::where('id_produksi_nyusup', $produksiId)
            ->sum(DB::raw('CAST(hasil AS UNSIGNED)'));

        // ======================
        // GLOBAL UKURAN + GRADE (KW)
        // ======================
        $globalUkuranGrade = DetailBarangDikerjakan::query()
            ->where('detail_barang_dikerjakan.id_produksi_nyusup', $produksiId)
            ->join('barang_setengah_jadi_hp as bsj', 'bsj.id', '=', 'detail_barang_dikerjakan.id_barang_setengah_jadi_hp')
            ->join('ukurans', 'ukurans.id', '=', 'bsj.id_ukuran')
            ->join('grades', 'grades.id', '=', 'bsj.id_grade')
            ->selectRaw('
    CONCAT(
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.panjang, 2)), " x ",
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.lebar, 2)), " x ",
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.tebal, 2))
    ) AS ukuran,
    grades.nama_grade AS kw,
    SUM(CAST(detail_barang_dikerjakan.hasil AS UNSIGNED)) AS total
')

            ->groupBy('ukuran', 'grades.nama_grade')
            ->orderBy('ukuran')
            ->orderBy('grades.nama_grade')
            ->get();


        // ======================
        // GLOBAL UKURAN (SEMUA GRADE)
        // ======================
        $globalUkuran = DetailBarangDikerjakan::query()
            ->where('detail_barang_dikerjakan.id_produksi_nyusup', $produksiId)
            ->join('barang_setengah_jadi_hp as bsj', 'bsj.id', '=', 'detail_barang_dikerjakan.id_barang_setengah_jadi_hp')
            ->join('ukurans', 'ukurans.id', '=', 'bsj.id_ukuran')
            ->selectRaw('
    CONCAT(
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.panjang, 2)), " x ",
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.lebar, 2)), " x ",
        TRIM(TRAILING ".00" FROM FORMAT(ukurans.tebal, 2))
    ) AS ukuran,
    SUM(CAST(detail_barang_dikerjakan.hasil AS UNSIGNED)) AS total
')

            ->groupBy('ukuran')
            ->orderBy('ukuran')
            ->get();


        $this->summary = [
            'totalAll'           => $totalAll,
            'globalUkuranGrade'  => $globalUkuranGrade,
            'globalUkuran'       => $globalUkuran,
        ];
    }
}
