<?php

namespace App\Filament\Resources\NotaKayus\Schemas;

use App\Models\KayuMasuk;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotaKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_kayu_masuk')
                    ->label('Detail Kayu Masuk')
                    ->options(
                        KayuMasuk::query()
                            ->with('penggunaanSupplier')
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(function ($kayu_masuk) {
                                return [
                                    $kayu_masuk->id => "{$kayu_masuk->tgl_kayu_masuk} - Seri : {$kayu_masuk->seri} - Supplier : {$kayu_masuk->penggunaanSupplier?->nama_supplier}",
                                ];
                            })
                    )
                    ->searchable()
                    ->reactive() // penting! supaya bisa trigger perubahan
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) {
                            $set('no_nota', null);
                            return;
                        }

                        $kayuMasuk = KayuMasuk::with('penggunaanSupplier')->find($state);
                        if (!$kayuMasuk) {
                            $set('no_nota', null);
                            return;
                        }

                        // Ubah format tanggal: 2025-11-05 -> 05112025
                        $tgl = \Carbon\Carbon::parse($kayuMasuk->tgl_kayu_masuk)->format('dmY');

                        $seri = $kayuMasuk->seri;
                        $supplierId = $kayuMasuk->id_supplier_kayus;

                        // Hitung urutan berdasarkan tanggal kayu masuk
                        $countToday = KayuMasuk::whereDate('tgl_kayu_masuk', $kayuMasuk->tgl_kayu_masuk)->count() + 1;
                        $noUrut = str_pad($countToday, 3, '0', STR_PAD_LEFT);

                        // Bentuk nilai akhir tanpa tanda hubung
                        $noNota = "{$tgl}{$seri}{$supplierId}{$noUrut}";

                        $set('no_nota', $noNota);
                    })
                    ->required(),

                TextInput::make('no_nota')
                    ->label('No Nota')
                    ->disabled()
                    ->dehydrated(true)
                    ->required(),
                TextInput::make('penanggung_jawab')
                    ->required(),
                TextInput::make('penerima')
                    ->required(),
                TextInput::make('satpam')
                    ->required(),
            ]);
    }
}
