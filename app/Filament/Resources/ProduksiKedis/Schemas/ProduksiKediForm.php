<?php

namespace App\Filament\Resources\ProduksiKedis\Schemas;

use App\Models\ProduksiKedi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ProduksiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('tanggal')
                    ->label('Tanggal Produksi')

                    ->default(fn() => now()->addDay())
                    ->displayFormat('d F Y')
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, $fail) {
                                $exists = ProduksiKedi::whereDate('tanggal', $value)->exists();

                                if ($exists) {
                                    $fail('Tanggal ini sudah digunakan. Pilih tanggal lain.');
                                }
                            };
                        },
                    ]),

                Select::make('status')
                    ->label('Status Produksi')
                    ->options([
                        'masuk'   => 'Masuk',
                        'bongkar' => 'Bongkar',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Status produksi wajib dipilih.',
                    ]),
            ]);
    }
}
