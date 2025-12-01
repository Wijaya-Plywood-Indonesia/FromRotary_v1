<?php

namespace App\Filament\Resources\KayuMasuks\RelationManagers;

use App\Filament\Resources\DetailKayuMasuks\Schemas\DetailKayuMasukForm;
use App\Filament\Resources\DetailKayuMasuks\Tables\DetailKayuMasuksTable;
use App\Models\DetailKayuMasuk;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailMasukanKayuRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailMasukanKayu';
    protected static ?string $title = 'Detail Kayu Masuk';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return DetailKayuMasukForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailKayuMasuksTable::configure($table);
    }

    /** ⭐ TAMBAHKAN INI */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('total_kubikasi')
                ->label(function () {
                    // Ambil parent ID dari KayuMasuk
                    $parentId = $this->ownerRecord?->id;

                    // Hitung hanya detail yang memiliki id_kayu_masuk sama
                    $total = DetailKayuMasuk::where('id_kayu_masuk', $parentId)
                        ->get()
                        ->sum(
                            fn($item) =>
                            ($item->diameter ?? 0)
                            * ($item->diameter ?? 0)
                            * ($item->jumlah_batang ?? 0)
                            * 0.785 / 1_000_000
                        );

                    return 'Total Kubikasi = ' . number_format($total, 6, ',', '.') . ' m³';
                })
                ->disabled()
                ->icon('heroicon-o-cube')
                ->button()
                ->outlined()
                ->color('gray'),
        ];
    }

    public function incrementJumlah($id)
    {
        if ($item = DetailKayuMasuk::find($id)) {
            $item->increment('jumlah_batang');
        }
    }

    public function decrementJumlah($id)
    {
        if ($item = DetailKayuMasuk::find($id)) {
            if ($item->jumlah_batang > 0) {
                $item->decrement('jumlah_batang');
            }
        }
    }
}