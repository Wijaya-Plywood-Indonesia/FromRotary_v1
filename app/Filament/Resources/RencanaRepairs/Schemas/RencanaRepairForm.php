<?php

namespace App\Filament\Resources\RencanaRepairs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Ukuran;
use App\Models\JenisKayu;
use App\Models\ModalRepair;
use App\Models\RencanaPegawai;

class RencanaRepairForm
{
    public static function configure(Schema $schema, $record = null): Schema
    {
        $produksiId = $record?->id_produksi_repair
            ?? request()->query('produksi_id')
            ?? $schema->getLivewire()->ownerRecord?->id
            ?? request()->route('record');

        // Ambil semua modal repair untuk produksi ini
        $modalRepairs = ModalRepair::where('id_produksi_repair', $produksiId)
            ->with(['ukuran', 'jenisKayu'])
            ->get();

        // Ambil semua opsi unik untuk Ukuran, Jenis Kayu, KW dari modal repair
        $ukuranOptions = $modalRepairs->pluck('ukuran')->unique('id')->mapWithKeys(fn($u) => [$u->id => $u->dimensi])->toArray();
        $jenisKayuOptions = $modalRepairs->pluck('jenisKayu')->unique('id')->mapWithKeys(fn($k) => [$k->id => $k->nama_kayu])->toArray();
        $kwOptions = $modalRepairs->pluck('kw')->unique()->mapWithKeys(fn($kw) => [$kw => $kw])->toArray();

        // Ambil semua rencana pegawai
        $rencanaPegawaiOptions = RencanaPegawai::where('id_produksi_repair', $produksiId)
            ->with('pegawai')
            ->orderBy('nomor_meja')
            ->get()
            ->mapWithKeys(fn($rp) => [
                $rp->id => "Meja {$rp->nomor_meja} - {$rp->pegawai?->nama_pegawai} ({$rp->pegawai?->kode_pegawai})"
            ])->toArray();

        return $schema->schema([
            Select::make('id_ukuran')
                ->label('Ukuran')
                ->options($ukuranOptions)
                ->searchable()
                ->preload()
                ->required(),

            Select::make('id_jenis_kayu')
                ->label('Jenis Kayu')
                ->options($jenisKayuOptions)
                ->searchable()
                ->preload()
                ->required(),

            Select::make('kw')
                ->label('KW')
                ->options($kwOptions)
                ->searchable()
                ->preload()
                ->required(),

            Select::make('id_rencana_pegawai')
                ->label('Penempatan Meja')
                ->options($rencanaPegawaiOptions)
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Pilih Meja...'),
        ]);
    }
}
