<?php

namespace App\Filament\Resources\ProduksiPressDryers\Pages;

use App\Filament\Resources\ProduksiPressDryers\ProduksiPressDryerResource;
use App\Models\ProduksiPressDryer;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateProduksiPressDryer extends CreateRecord
{
    protected static string $resource = ProduksiPressDryerResource::class;

    protected function beforeCreate(): void
    {
        $tanggal = $this->data['tanggal_produksi'] ?? null;
        $shift = $this->data['shift'] ?? null;

        if ($tanggal && $shift) {
            $exists = ProduksiPressDryer::whereDate('tanggal_produksi', $tanggal)
                ->where('shift', $shift)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'data.shift' => "Laporan untuk tanggal ini dengan Shift {$shift} sudah ada.",
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}