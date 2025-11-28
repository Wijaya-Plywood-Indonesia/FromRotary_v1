<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKayu;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanKayuMasukController extends Controller
{
    // ❗ Query dipisah agar index() dan export() bisa pakai bersama
    private function baseQuery()
    {
        return DB::table('detail_turusan_kayus')
            ->join('kayu_masuks', 'kayu_masuks.id', '=', 'detail_turusan_kayus.id_kayu_masuk')
            ->join('supplier_kayus', 'supplier_kayus.id', '=', 'kayu_masuks.id_supplier_kayus')
            ->join('jenis_kayus', 'jenis_kayus.id', '=', 'detail_turusan_kayus.jenis_kayu_id')
            ->leftJoin('lahans', 'lahans.id', '=', 'detail_turusan_kayus.lahan_id')
            ->join('harga_kayus', function ($join) {
                $join->on('harga_kayus.id_jenis_kayu', '=', 'detail_turusan_kayus.jenis_kayu_id')
                    ->whereColumn('harga_kayus.panjang', 'detail_turusan_kayus.panjang')
                    ->whereRaw('detail_turusan_kayus.diameter BETWEEN harga_kayus.diameter_terkecil AND harga_kayus.diameter_terbesar');
            })
            ->select([
                DB::raw('DATE_FORMAT(kayu_masuks.tgl_kayu_masuk, "%e/%c/%Y") as tanggal'),
                'supplier_kayus.nama_supplier as nama',
                'kayu_masuks.seri',
                'detail_turusan_kayus.panjang',
                'jenis_kayus.nama_kayu as jenis',
                'lahans.kode_lahan as lahan',
                DB::raw('SUM(detail_turusan_kayus.kuantitas) as banyak'),
                DB::raw('SUM(
                    detail_turusan_kayus.panjang
                    * detail_turusan_kayus.diameter
                    * detail_turusan_kayus.diameter
                    * detail_turusan_kayus.kuantitas
                    * 0.785 / 1000000
                ) as m3'),
                DB::raw('SUM(
                    (
                        detail_turusan_kayus.panjang
                        * detail_turusan_kayus.diameter
                        * detail_turusan_kayus.diameter
                        * detail_turusan_kayus.kuantitas
                        * 0.785 / 1000000
                    ) * harga_kayus.harga_beli * 1000
                ) as poin'),
            ])
            ->groupBy(
                'kayu_masuks.tgl_kayu_masuk',
                'supplier_kayus.nama_supplier',
                'kayu_masuks.seri',
                'detail_turusan_kayus.panjang',
                'jenis_kayus.nama_kayu',
                'lahans.kode_lahan'
            )
            ->orderBy('kayu_masuks.tgl_kayu_masuk', 'desc');
    }

    // =======================
    //   HALAMAN BLADE
    // =======================
    public function index()
    {
        $data = $this->baseQuery()->get();
        return view('nota-kayu.laporan-kayu', compact('data'));
    }

    // =======================
    //   EXPORT EXCEL
    // =======================
    public function export()
    {
        // kolom export
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
