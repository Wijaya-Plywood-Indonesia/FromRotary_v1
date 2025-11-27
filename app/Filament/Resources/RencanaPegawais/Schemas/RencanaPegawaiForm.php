<?php

namespace App\Filament\Resources\RencanaPegawais\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use App\Models\Pegawai;
use App\Models\RencanaPegawai;

class RencanaPegawaiForm
{
    public static function configure(Schema $schema, ?int $rencanaRepairId = null): Schema
    {
        return $schema
            ->components([
                Hidden::make('id_rencana_repair')
                    ->default($rencanaRepairId),

                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->options(
                        Pegawai::query()
                            ->get()
                            ->mapWithKeys(fn($pegawai) => [
                                $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                            ])
                    )
                    ->searchable()
                    ->required(),

                // --- BAGIAN YANG DIPERBAIKI ---
                TextInput::make('nomor_meja')
                    ->label('Nomor Meja')
                    ->required()
                    ->numeric()
                    // Default mengambil dari SESSION. Jika tidak ada di session, baru ambil 1.
                    ->default(function () use ($rencanaRepairId) {
                        $sessionKey = 'last_nomor_meja_' . ($rencanaRepairId ?? 'global');
                        return session()->get($sessionKey, 1);
                    })
                    // Penting: live(onBlur: true) agar event jalan setelah user selesai mengetik/pindah kolom
                    ->live(onBlur: true)
                    // Simpan ke session setiap kali user mengubah angka
                    ->afterStateUpdated(function ($state) use ($rencanaRepairId) {
                        $sessionKey = 'last_nomor_meja_' . ($rencanaRepairId ?? 'global');
                        session()->put($sessionKey, $state);
                    }),
                // -----------------------------
            ]);
    }
}