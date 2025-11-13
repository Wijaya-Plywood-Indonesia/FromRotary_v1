<?php

namespace App\Filament\Resources\DetailTurunKayus\Pages;

use App\Filament\Resources\DetailTurunKayus\DetailTurunKayuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateDetailTurunKayu extends CreateRecord
{
    protected static string $resource = DetailTurunKayuResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Ambil array pegawai
        $pegawaiIds = $data['id_pegawai'] ?? [];

        // Pastikan dalam bentuk array
        if (!is_array($pegawaiIds)) {
            $pegawaiIds = [$pegawaiIds];
        }

        // Validasi minimal 1 pegawai
        if (empty($pegawaiIds)) {
            Notification::make()
                ->title('Error')
                ->body('Pilih minimal 1 pegawai!')
                ->danger()
                ->send();

            $this->halt();
        }

        // Hapus id_pegawai dari data
        unset($data['id_pegawai']);

        $firstRecord = null;
        $createdCount = 0;

        // Buat record untuk SETIAP pegawai
        foreach ($pegawaiIds as $pegawaiId) {
            $record = static::getModel()::create([
                ...$data,
                'id_pegawai' => $pegawaiId
            ]);

            // Simpan record pertama untuk redirect
            if ($firstRecord === null) {
                $firstRecord = $record;
            }

            $createdCount++;
        }

        // Notifikasi sukses
        Notification::make()
            ->title('Berhasil!')
            ->body("{$createdCount} data turun kayu berhasil dibuat.")
            ->success()
            ->send();

        return $firstRecord;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}