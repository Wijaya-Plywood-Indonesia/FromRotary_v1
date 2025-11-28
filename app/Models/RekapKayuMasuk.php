<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapKayuMasuk extends Model
{
    protected $table = 'detail_turusan_kayus';

    // Hanya digunakan untuk laporan, read-only
    protected $guarded = [];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(fn() => false);
        static::updating(fn() => false);
        static::deleting(fn() => false);
    }

    public function save(array $options = [])
    {
        return false;
    }

    public function delete()
    {
        return false;
    }

    public function update(array $attributes = [], array $options = [])
    {
        return false;
    }

    public static function create(array $attributes = [])
    {
        return false;
    }


    // =======================
    // RELASI (dipakai laporan)
    // =======================

    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }

    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'jenis_kayu_id');
    }


    // =======================
    // ACCESSOR OTOMATIS LAPORAN
    // =======================

    protected $appends = ['tanggal', 'nama', 'kubikasi', 'harga_satuan', 'total_harga'];

    public function getTanggalAttribute()
    {
        return $this->kayuMasuk->tgl_kayu_masuk ?? null;
    }

    public function getNamaAttribute()
    {
        return $this->kayuMasuk->penggunaanSupplier->nama_supplier
            ?? $this->kayuMasuk->penggunaanPenerima->nama_penerima
            ?? null;
    }

    public function getKubikasiAttribute()
    {
        $diameter = (float) ($this->diameter ?? 0);
        $jumlah = (float) ($this->kuantitas ?? 0);
        $panjang = (float) ($this->panjang ?? 0);

        return ($panjang * $diameter * $diameter * $jumlah * 0.785) / 1000000;
    }

    public function getHargaSatuanAttribute()
    {
        return (float) (
            \App\Models\HargaKayu::where('id_jenis_kayu', $this->id_jenis_kayu)
                ->where('grade', $this->grade)
                ->where('panjang', $this->panjang)
                ->where('diameter_terkecil', '<=', $this->diameter)
                ->where('diameter_terbesar', '>=', $this->diameter)
                ->value('harga_beli') ?? 0
        );
    }

    public function getTotalHargaAttribute()
    {
        return round(
            $this->harga_satuan * $this->kubikasi * 1000,
            2
        );
    }
}

