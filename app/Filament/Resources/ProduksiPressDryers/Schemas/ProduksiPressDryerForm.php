<?php

namespace App\Filament\Resources\ProduksiPressDryers\Schemas;

use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Carbon\Carbon;

class ProduksiPressDryerForm
{
    public static function configure($schema, $record = null)
    {
        return $schema->components([
            DatePicker::make('tanggal_produksi')
                ->label('Tanggal Produksi')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->format('Y-m-d')
                ->default(now()->addDay())
                ->required()
                ->live()
                // Pemicu pengecekan instan
                ->afterStateUpdated(fn ($state, $get, $set) => self::checkDuplication($state, $get('shift'), $set, $record)),

            Select::make('shift')
                ->label('Shift')
                ->options([
                    'PAGI' => 'Pagi',
                    'MALAM' => 'Malam',
                ])
                ->required()
                ->native(false)
                ->live()
                // Pemicu pengecekan instan
                ->afterStateUpdated(fn ($state, $get, $set) => self::checkDuplication($get('tanggal_produksi'), $state, $set, $record)),
            
            // ... komponen lainnya seperti kendala ...
        ]);
    }

    /**
     * Fungsi Helper untuk mengecek duplikasi secara Real-time
     */
    protected static function checkDuplication($tanggal, $shift, $set, $record)
    {
        if (blank($tanggal) || blank($shift)) return;

        $query = ProduksiPressDryer::whereDate('tanggal_produksi', $tanggal)
            ->where('shift', $shift);

        if ($record) {
            $query->where('id', '!=', $record->id);
        }

        if ($query->exists()) {
            Notification::make()
                ->title('Duplikasi Terdeteksi')
                ->body("Data produksi tanggal " . Carbon::parse($tanggal)->format('d/m/Y') . " Shift {$shift} sudah ada.")
                ->danger()
                ->send();
            
            // Opsional: kosongkan shift jika duplikat agar user memilih ulang
            $set('shift', null);
        }
    }
}