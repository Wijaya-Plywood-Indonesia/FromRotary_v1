<?php

namespace App\Filament\Resources\ProduksiKedis\Schemas;

use App\Models\ProduksiKedi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class ProduksiKediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('tanggal')
                    ->label('Tanggal Produksi')

                    ->default(fn () => now()->addDay())
                    ->displayFormat('d F Y')
                    ->required()
                    ->rules([
                        fn ($get, $record) =>
                            Rule::unique('produksi_kedi', 'tanggal')
                                ->where('status', $get('status'))
                                ->ignore($record?->id),
                    ])
                    ->validationMessages([
                        'unique' => 'Produksi dengan tanggal dan status ini sudah ada!',
                        'required' => 'Tanggal produksi wajib diisi.',
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