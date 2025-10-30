<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierKayu extends Model
{
    //
    // use HasFactory;

    protected $table = 'supplier_kayus';

    protected $fillable = [
        'nama_supplier',
        'no_telepon',
        'nik',
        'jenis_kelamin',
        'alamat',
        'jenis_bank',
        'no_rekening',
        'status_supplier',
    ];

    /**
     * Cast kolom tertentu ke tipe data tertentu.
     */
    protected $casts = [
        'jenis_kelamin_pegawai' => 'integer',
        'status_supplier' => 'integer',
        'nik' => 'encrypted', // ✅ otomatis enkripsi & dekripsi
    ];

    /**
     * Accessor opsional untuk menampilkan jenis kelamin dengan teks.
     */
    public function getJenisKelaminPegawaiLabelAttribute(): string
    {
        return $this->jenis_kelamin_pegawai ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Accessor opsional untuk status supplier.
     */
    public function getStatusSupplierLabelAttribute(): string
    {
        return $this->status_supplier ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Relasi contoh ke tabel lain (misal: kayu_masuk)
     */
    // public function kayuMasuk()
    // {
    //     return $this->hasMany(KayuMasuk::class, 'supplier_id');
    // }
}
