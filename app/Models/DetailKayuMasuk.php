<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKayuMasuk extends Model
{
    //
    protected $table = 'detail_kayu_masuks';

    protected $fillable = [
        'id_kayu_masuk',
        'id_jenis_kayu',
        'id_lahan',
        'diameter',
        'panjang',
        'grade',
        'jumlah_batang',
        'keterangan',
    ];

    /**
     * Relasi ke model KayuMasuk
     * (Setiap detail kayu masuk dimiliki oleh satu kayu masuk)
     */
    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }
    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu', 'id');
    }
    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'id_lahan');
    }
    protected $appends = ['kubikasi', 'harga_satuan', 'total_harga'];

    public function getKubikasiAttribute()
    {
        $diameter = (float) ($this->diameter ?? 0); // cm
        $jumlah = (float) ($this->jumlah_batang ?? 0);
        $panjang = (float) ($this->panjang ?? 0);

        // formula: diameter * jumlah * 0.785 / 1_000_000
        // kembalikan float dengan presisi cukup tinggi
        $kubikasi = ($panjang * $diameter * $diameter * $jumlah * 0.785) / 1000000;

        return $kubikasi; // mis. 0.123456789
    }
    public function getHargaSatuanAttribute()
    {
        $harga = \App\Models\HargaKayu::where('id_jenis_kayu', $this->id_jenis_kayu)
            ->where('grade', $this->grade)
            ->where('panjang', $this->panjang)
            ->where('diameter_terkecil', '<=', $this->diameter)
            ->where('diameter_terbesar', '>=', $this->diameter)
            ->value('harga_beli');

        return (float) ($harga ?? 0);
    }

    public function getTotalHargaAttribute()
    {
        $hargaSatuan = $this->harga_satuan; // float
        $kubikasiRaw = $this->getAttribute('kubikasi'); // akan memanggil accessor di atas

        // Kalkulasi presisi lalu lakukan pembulatan akhir
        $total = $hargaSatuan * $kubikasiRaw * 1000;

        // Jika kamu menyimpan/menampilkan dalam rupiah tanpa decimal, gunakan round($total, 0)
        // Jika butuh 2 desimal, gunakan round($total, 2)
        return round($total, 2);
    }

    public function hargaKayu()
    {
        return $this->belongsTo(HargaKayu::class, 'id_jenis_kayu', 'id_jenis_kayu')
            ->whereColumn('harga_kayus.grade', 'detail_kayu_masuks.grade')
            ->whereColumn('harga_kayus.panjang', 'detail_kayu_masuks.panjang')
            ->whereRaw('? BETWEEN harga_kayus.diameter_terkecil AND harga_kayus.diameter_terbesar', [$this->diameter]);
    }

}
