<?php

namespace App\Filament\Resources\RencanaRepairs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema; // PAKAI Form, BUKAN Schema!
use App\Models\Ukuran;
use App\Models\JenisKayu;
use App\Models\RencanaPegawai;
use App\Models\RencanaRepair;

class RencanaRepairForm
{
    public static function configure(Schema $form, $record = null): Schema
    {
        // Ambil ID produksi — PASTI BENAR di RelationManager
        $produksiId = $record?->id_produksi_repair
            ?? request()->query('produksi_id')
            ?? $form->getLivewire()->ownerRecord?->id
            ?? request()->route('record');

        // 1. UKURAN — INGAT YANG TERAKHIR
        $ukuranOptions = Ukuran::orderBy('panjang')
            ->orderBy('lebar')
            ->get()
            ->mapWithKeys(fn($u) => [$u->id => $u->dimensi . ' × 0.5'])
            ->toArray();

        // 2. JENIS KAYU — INGAT YANG TERAKHIR
        $jenisKayuOptions = JenisKayu::orderBy('nama_kayu')
            ->get()
            ->mapWithKeys(fn($k) => [$k->id => $k->nama_kayu ?? "Kayu ID {$k->id}"])
            ->toArray();

        // 3. MEJA & PEGAWAI — REAL-TIME
        $usedRencanaPegawaiIds = RencanaRepair::where('id_produksi_repair', $produksiId)
            ->when($record, fn($q) => $q->where('id', '!=', $record->id))
            ->pluck('id_rencana_pegawai')
            ->toArray();

        $mejaOptions = RencanaPegawai::where('id_produksi_repair', $produksiId)
            ->whereNotIn('id', $usedRencanaPegawaiIds)   // ← INI KUNCI ANTI DUPLIKAT
            ->with('pegawai')
            ->orderBy('nomor_meja')
            ->get()
            ->mapWithKeys(fn($rp) => [
                $rp->id => "Meja {$rp->nomor_meja} - {$rp->pegawai?->nama_pegawai} ({$rp->pegawai?->kode_pegawai})"
            ])
            ->toArray();

        // Bonus: Ambil dari tabel Livewire jika ada data baru
        $livewire = $form->getLivewire();
        if (method_exists($livewire, 'getTableRecords')) {
            foreach ($livewire->getTableRecords() as $rec) {
                if ($rec->id_produksi_repair == $produksiId && $rec->pegawai) {
                    $mejaOptions[$rec->id] = "Meja {$rec->nomor_meja} - {$rec->pegawai->nama_pegawai} ({$rec->pegawai->kode_pegawai})";
                }
            }
        }

        return $form->schema([

            Select::make('id_ukuran')
                ->label('Ukuran')
                ->options($ukuranOptions)
                ->searchable()
                ->preload()
                ->required()
                ->default(fn() => session('last_ukuran_repair'))
                ->afterStateUpdated(fn($state) => session(['last_ukuran_repair' => $state])),

            Select::make('id_jenis_kayu')
                ->label('Jenis Kayu')
                ->options($jenisKayuOptions)
                ->searchable()
                ->preload()
                ->required()
                ->default(fn() => session('last_jenis_kayu_repair'))
                ->afterStateUpdated(fn($state) => session(['last_jenis_kayu_repair' => $state])),

            TextInput::make('kw')
                ->label('KW')
                ->required()
                ->maxLength(10)
                ->placeholder('1, 2, 3,')
                ->default(fn() => session('last_kw_repair'))
                ->numeric()
                ->afterStateUpdated(fn($state) => session(['last_kw_repair' => $state])),

            Select::make('id_rencana_pegawai')
                ->label('Penempatan Meja')
                ->options($mejaOptions)
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Pilih Meja...')
                ->reactive(),
        ]);
    }
}