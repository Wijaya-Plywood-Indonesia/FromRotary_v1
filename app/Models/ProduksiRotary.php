<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class ProduksiRotary extends Model
{
    //
    protected $fillable = [
        'id_mesin',
        'tgl_produksi',
        'kendala',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }
    public function produksi_rotaries()
    {
        return $this->hasMany(ProduksiRotary::class, 'id_produksi');
    }
    public function detailPegawaiRotary()
    {
        return $this->hasMany(PegawaiRotary::class, 'id_produksi');
    }
    public function detailLahanRotary()
    {
        return $this->hasMany(PenggunaanLahanRotary::class, 'id_produksi');
    }
    public function detailValidasiHasilRotary()
    {
        return $this->hasMany(ValidasiHasilRotary::class, 'id_produksi');
    }
    public function detailGantiPisauRotary()
    {
        return $this->hasMany(GantiPisauRotary::class, 'id_produksi');
    }
    public function detailPaletRotary()
    {
        return $this->hasMany(DetailHasilPaletRotary::class, 'id_produksi');
    }
    public function detailKayuPecah()
    {
        return $this->hasMany(KayuPecahRotary::class, 'id_produksi');
    }
    protected static function booted()
    {
        static::deleting(function ($record) {
            try {
                // Coba hapus record anak manual, jika mau
            } catch (QueryException $e) {
                if ($e->getCode() == '23000') {
                    Notification::make()
                        ->title('Data tidak dapat dihapus')
                        ->body('Data ini masih digunakan pada tabel lain.')
                        ->danger()
                        ->send();

                    return false; // Batalkan penghapusan
                }
            }
        });
    }

}
