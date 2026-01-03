<?php

namespace App\Filament\Resources\ProduksiJoints\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use App\Models\ProduksiJoint;
use App\Models\HasilJoint;

class ProduksiJointSummaryWidget extends Widget
{
    protected string $view = 'filament.resources.produksi-joint.widgets.summary';

    protected int|string|array $columnSpan = 'full';

    public ?ProduksiJoint $record = null;

    public array $summary = [];

    public function mount(?ProduksiJoint $record = null): void
    {
        if (! $record) {
            return;
        }

        $produksiId = $record->id;

        // ======================
        // TOTAL KESELURUHAN
        // ======================
        $totalAll = HasilJoint::where('id_produksi_joint', $produksiId)
            ->sum(DB::raw('CAST(jumlah AS UNSIGNED)'));

        // ======================
        // GLOBAL UKURAN + KW
        // ======================
        $globalUkuranKw = HasilJoint::query()
            ->where('id_produksi_joint', $produksiId)
            ->join('ukurans', 'ukurans.id', '=', 'hasil_joint.id_ukuran')
            ->selectRaw('
                CONCAT(
                    TRIM(TRAILING ".00" FROM CAST(ukurans.panjang AS CHAR)), " x ",
                    TRIM(TRAILING ".00" FROM CAST(ukurans.lebar AS CHAR)), " x ",
                    TRIM(TRAILING "0" FROM TRIM(TRAILING "." FROM CAST(ukurans.tebal AS CHAR)))
                ) AS ukuran,
                hasil_joint.kw,
                SUM(CAST(hasil_joint.jumlah AS UNSIGNED)) AS total
            ')
            ->groupBy('ukuran', 'hasil_joint.kw')
            ->orderBy('ukuran')
            ->orderBy('hasil_joint.kw')
            ->get();

        // ======================
        // GLOBAL UKURAN (SEMUA KW)
        // ======================
        $globalUkuran = HasilJoint::query()
            ->where('id_produksi_joint', $produksiId)
            ->join('ukurans', 'ukurans.id', '=', 'hasil_joint.id_ukuran')
            ->selectRaw('
                CONCAT(
                    TRIM(TRAILING ".00" FROM CAST(ukurans.panjang AS CHAR)), " x ",
                    TRIM(TRAILING ".00" FROM CAST(ukurans.lebar AS CHAR)), " x ",
                    TRIM(TRAILING "0" FROM TRIM(TRAILING "." FROM CAST(ukurans.tebal AS CHAR)))
                ) AS ukuran,
                SUM(CAST(hasil_joint.jumlah AS UNSIGNED)) AS total
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
