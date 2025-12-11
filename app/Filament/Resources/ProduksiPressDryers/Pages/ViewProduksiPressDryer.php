<?php

namespace App\Filament\Resources\ProduksiPressDryers\Pages;

use App\Filament\Resources\ProduksiPressDryers\ProduksiPressDryerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiPressDryer extends ViewRecord
{
    protected static string $resource = ProduksiPressDryerResource::class;

    // 🔥 WAJIB: tampilkan relation manager di halaman View
    protected static bool $showRelationManagers = true;

    // 🔥 WAJIB: izinkan tombol-tombol header tampil (termasuk CREATE)
    protected static bool $canViewAny = true;

    protected function getHeaderActions(): array
    {
        return [
        EditAction::make()
            ->hidden(function () {
                $record = $this->getRecord();

                // Jika tidak ada validasi → tombol tetap muncul
                if (!$record->validasiTerakhir) {
                    return false;
                }

                // Jika status terakhir = divalidasi → sembunyikan tombol
                return $record->validasiTerakhir->status === 'divalidasi';
            }),
    ];
    }
}
