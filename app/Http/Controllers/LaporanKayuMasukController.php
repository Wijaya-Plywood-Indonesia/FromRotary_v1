<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKayu;
use Illuminate\Support\Facades\DB;
use Excel;

class LaporanKayuMasukController extends Controller
{
    /**
     * =======================================
     * BASE SQL QUERY – dipakai index & export
     * (SESUAI SQL FINAL — AMAN FLOATING)
     * =======================================
     */
    private function baseQuery()
    {
        // ================================
        // RUMUS KUBIKASI TUNGGAL
        // ================================
        $m3Formula = "
            CAST(
                detail_turusan_kayus.panjang
              * detail_turusan_kayus.diameter
              * detail_turusan_kayus.diameter
              * detail_turusan_kayus.kuantitas
              * 0.785 / 1000000
            AS DECIMAL(18,8))
        ";

        return DB::table('detail_turusan_kayus')

            // ======================
            // RELASI — TIDAK DIUBAH
            // ======================
            ->join('kayu_masuks', 'kayu_masuks.id', '=', 'detail_turusan_kayus.id_kayu_masuk')
            ->join('supplier_kayus', 'supplier_kayus.id', '=', 'kayu_masuks.id_supplier_kayus')
            ->join('jenis_kayus', 'jenis_kayus.id', '=', 'detail_turusan_kayus.jenis_kayu_id')
            ->leftJoin('lahans', 'lahans.id', '=', 'detail_turusan_kayus.lahan_id')

            // ======================
            // JOIN HARGA — anti dobel
            // ======================
            ->leftJoin('harga_kayus AS hk', function ($join) {
                $join->on('hk.id', '=', DB::raw("
                    (
                        SELECT hx.id
                        FROM harga_kayus hx
                        WHERE hx.id_jenis_kayu = detail_turusan_kayus.jenis_kayu_id
                          AND hx.grade         = detail_turusan_kayus.grade
                          AND hx.panjang       = detail_turusan_kayus.panjang
                          AND detail_turusan_kayus.diameter
                              BETWEEN hx.diameter_terkecil
                              AND hx.diameter_terbesar
                        ORDER BY hx.diameter_terkecil DESC
                        LIMIT 1
                    )
                "));
            })

            ->select([

                // ======================
                // HEADER (TETAP)
                // ======================
                DB::raw('DATE(kayu_masuks.tgl_kayu_masuk) AS tanggal'),
                'supplier_kayus.nama_supplier AS nama',
                'kayu_masuks.seri',
                'detail_turusan_kayus.panjang',
                'jenis_kayus.nama_kayu AS jenis',
                'lahans.kode_lahan AS lahan',

                // ======================
                // TOTAL BATANG
                // ======================
                DB::raw('SUM(detail_turusan_kayus.kuantitas) AS banyak'),

                // ======================
                // KUBIKASI
                // ROUND PER BARIS
                // ======================
                DB::raw("
                    ROUND(
                        SUM(
                            ROUND($m3Formula, 4)
                        ),
                    4) AS m3
                "),

                // ======================
                // POIN
                // TIDAK DIBULATKAN INT
                // ======================
                DB::raw("
                    ROUND(
                        SUM(
                            ROUND($m3Formula, 4)
                            * CAST( COALESCE(hk.harga_beli,0) AS DECIMAL(12,2) )
                            * 1000
                        ),
                    2) AS poin
                "),
            ])

            ->groupBy([
                DB::raw('DATE(kayu_masuks.tgl_kayu_masuk)'),
                'supplier_kayus.nama_supplier',
                'kayu_masuks.seri',
                'detail_turusan_kayus.panjang',
                'jenis_kayus.nama_kayu',
                'lahans.kode_lahan',
            ])

            ->orderByDesc('kayu_masuks.seri');
    }


    /**
     * ======================
     * INDEX
     * ======================
     */
    public function index()
    {
        $data = $this->baseQuery()->get();

        return view('nota-kayu.laporan-kayu', compact('data'));
    }

    /**
     * =======================
     * EXPORT EXCEL ✅
     * =======================
     */
    public function export()
    {
        $columns = [
            ['label' => 'Tanggal', 'field' => 'tanggal'],
            ['label' => 'Nama', 'field' => 'nama'],
            ['label' => 'Seri', 'field' => 'seri'],
            ['label' => 'Panjang', 'field' => 'panjang'],
            ['label' => 'Jenis', 'field' => 'jenis'],
            ['label' => 'Lahan', 'field' => 'lahan'],
            ['label' => 'Banyak', 'field' => 'banyak'],
            ['label' => 'M3', 'field' => 'm3'],
            ['label' => 'Poin', 'field' => 'poin'],
        ];

        return Excel::download(
            new LaporanKayu($this->baseQuery(), $columns),
            'laporan_kayu.xlsx'
        );
    }

}
