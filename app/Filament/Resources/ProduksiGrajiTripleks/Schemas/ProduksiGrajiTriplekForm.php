<?php

namespace App\Filament\Resources\ProduksiGrajiTripleks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Validation\Rule;
use App\Models\ProduksiGrajitriplek;

class ProduksiGrajiTriplekForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /**
             * ==========================
             * 📅 TANGGAL PRODUKSI
             * ==========================
             */
            DatePicker::make('tanggal_produksi')
                ->label('Tanggal Produksi')
                ->default(fn () => now()->addDay())
                ->displayFormat('d F Y')
                ->required()
                ->reactive()
                ->rule(function (callable $get, ?ProduksiGrajitriplek $record) {
                    return Rule::unique('produksi_graji_triplek', 'tanggal_produksi')
                        ->where(fn ($query) =>
                            $query->where('status', $get('status'))
                        )
                        ->ignore($record?->id);
                }),

            /**
             * ==========================
             * ⚙️ STATUS PRODUKSI
             * ==========================
             */
            Select::make('status')
                ->label('Status Produksi')
                ->options([
                    'graji manual'   => 'Graji Manual',
                    'graji otomatis' => 'Graji Otomatis',
                ])
                ->required()
                ->reactive()
                ->rule(function (callable $get, ?ProduksiGrajitriplek $record) {
                    return Rule::unique('produksi_graji_triplek', 'status')
                        ->where(fn ($query) =>
                            $query->whereDate('tanggal_produksi', $get('tanggal_produksi'))
                        )
                        ->ignore($record?->id);
                }),
        ]);
    }
}
