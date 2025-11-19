<?php

namespace App\Filament\Resources\ProduksiStiks\Pages;

use App\Filament\Resources\ProduksiStiks\ProduksiStikResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduksiStik extends ViewRecord
{
    protected static string $resource = ProduksiStikResource::class;

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
