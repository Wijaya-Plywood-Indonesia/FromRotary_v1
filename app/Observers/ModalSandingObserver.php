<?php

namespace App\Observers;

use App\Models\HasilSanding;
use App\Models\ModalSanding;
use Filament\Notifications\Notification;

class ModalSandingObserver
{
    /**
     * Handle the ModalSanding "created" event.
     */
    public function created(ModalSanding $modalSanding): void
    {
        // Cek apakah kombinasi sudah ada
        $exists = HasilSanding::where('id_produksi_sanding', $modalSanding->id_produksi_sanding)
            ->where('id_barang_setengah_jadi', $modalSanding->id_barang_setengah_jadi)
            ->where('no_palet', $modalSanding->no_palet)
            ->exists();

        if ($exists) {
            // Tampilkan notifikasi Filament
            Notification::make()
                ->title('Data Duplikat')
                ->body('Kombinasi produksi, barang setengah jadi, dan nomor palet sudah pernah dimasukkan.')
                ->danger()
                ->send();

            // Stop proses agar tidak insert
            return;
        }

        // Jika belum ada → buat hasil sanding baru
        HasilSanding::create([
            'id_produksi_sanding' => $modalSanding->id_produksi_sanding,
            'id_barang_setengah_jadi' => $modalSanding->id_barang_setengah_jadi,
            'kuantitas' => $modalSanding->kuantitas,
            'jumlah_sanding_face' => $modalSanding->jumlah_sanding_face,
            'jumlah_sanding_back' => $modalSanding->jumlah_sanding_back,
            //'no_palet' => $modalSanding->no_palet,
            'status' => 'Belum Sanding',
        ]);
    }

    /**
     * Handle the ModalSanding "updated" event.
     */
    public function updated(ModalSanding $modalSanding): void
    {
        //
    }

    /**
     * Handle the ModalSanding "deleted" event.
     */
    public function deleted(ModalSanding $modalSanding): void
    {
        //
    }

    /**
     * Handle the ModalSanding "restored" event.
     */
    public function restored(ModalSanding $modalSanding): void
    {
        //
    }

    /**
     * Handle the ModalSanding "force deleted" event.
     */
    public function forceDeleted(ModalSanding $modalSanding): void
    {
        //
    }
}
