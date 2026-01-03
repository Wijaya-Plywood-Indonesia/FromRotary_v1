<?php

namespace App\Filament\Resources\ProduksiStiks\Schemas;

use App\Models\ProduksiStik;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Carbon\Carbon;

class ProduksiStikForm
{
    public static function configure(Schema $schema, $record = null): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_produksi')
                    ->label('Pilih Tanggal Laporan')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now()->addDays(30))
                    ->default(now()->addDay())
                    ->live()
                    ->closeOnDateSelection()
                    ->suffixIcon('heroicon-o-calendar')
                    ->suffixIconColor('primary')
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, $fail) {
                                $exists = ProduksiStik::whereDate('tanggal_produksi', $value)->exists();

                                if ($exists) {
                                    $fail('Tanggal ini sudah digunakan. Pilih tanggal lain.');
                                }
                            };
                        },
                    ])

            ]);
    }
}
