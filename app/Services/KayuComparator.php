<?php

namespace App\Services;

use App\Models\ComparisonRow;
use App\Models\DetailKayuMasuk;
use App\Models\DetailTurusanKayu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class KayuComparator
{
    public static function buildQuery(int $idKayuMasuk): Builder
    {
        $detail = DetailKayuMasuk::selectRaw('
                id_jenis_kayu,
                id_lahan,
                diameter,
                panjang,
                grade,
                SUM(jumlah_batang) as total_detail
            ')
            ->where('id_kayu_masuk', $idKayuMasuk)
            ->groupBy('id_jenis_kayu', 'id_lahan', 'diameter', 'panjang', 'grade');

        $turusan = DetailTurusanKayu::selectRaw('
                jenis_kayu_id,
                lahan_id,
                diameter,
                panjang,
                grade,
                COUNT(*) as total_turusan
            ')
            ->where('id_kayu_masuk', $idKayuMasuk)
            ->groupBy('jenis_kayu_id', 'lahan_id', 'diameter', 'panjang', 'grade');

        $sqlBuilder = DB::query()
            ->fromSub($detail, 'detail')
            ->leftJoinSub($turusan, 'turusan', function ($join) {
                $join->on('detail.id_jenis_kayu', '=', 'turusan.jenis_kayu_id')
                    ->on('detail.id_lahan', '=', 'turusan.lahan_id')
                    ->on('detail.diameter', '=', 'turusan.diameter')
                    ->on('detail.panjang', '=', 'turusan.panjang')
                    ->on('detail.grade', '=', 'turusan.grade');
            })
            ->selectRaw('
                ROW_NUMBER() OVER () AS id,
                detail.id_jenis_kayu,
                detail.id_lahan,
                detail.diameter,
                detail.panjang,
                detail.grade,
                detail.total_detail AS detail_jumlah,
                COALESCE(turusan.total_turusan, 0) AS turusan_jumlah,
                (detail.total_detail - COALESCE(turusan.total_turusan, 0)) AS selisih
            ');

        // ALIAS FINAL WAJIB = comparison_rows
        return ComparisonRow::query()
            ->fromSub($sqlBuilder, 'comparison_rows')
            ->select('*');
    }
}
