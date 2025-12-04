<?php

namespace App\Filament\Resources\RencanaRepairs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
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

        return $schema->schema([
            Select::make('id_modal_repair')
                ->label('Pilih Kayu (Ukuran - Jenis - KW)')
                ->options(function () use ($produksiId) {
                    return ModalRepair::where('id_produksi_repair', $produksiId)
                        ->with(['ukuran', 'jenisKayu'])
                        ->get()
                        ->mapWithKeys(fn($modal) => [
                            $modal->id => sprintf(
                                '%s × 0.5 | %s | KW %s | Stok: %s pcs',
                                $modal->ukuran->dimensi ?? '-',
                                $modal->jenisKayu->nama_kayu ?? '-',
                                $modal->kw,
                                $modal->jumlah
                            )
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state) {
                    if ($state) {
                        $modal = ModalRepair::find($state);
                        if ($modal) {
                            $set('kw', $modal->kw);
                        }
                    } else {
                        $set('kw', null);
                    }
                })
                ->placeholder('🔍 Pilih modal kayu yang tersedia...')
                ->helperText('Pilih kombinasi Ukuran, Jenis Kayu, dan KW yang tersedia'),

            Select::make('id_rencana_pegawai')
                ->label('Penempatan Meja & Pegawai')
                ->options(function () use ($produksiId) {
                    return RencanaPegawai::where('id_produksi_repair', $produksiId)
                        ->with('pegawai')
                        ->orderBy('nomor_meja')
                        ->get()
                        ->mapWithKeys(fn($rp) => [
                            $rp->id => sprintf(
                                'Meja %s - %s (%s)',
                                $rp->nomor_meja,
                                $rp->pegawai?->nama_pegawai ?? '-',
                                $rp->pegawai?->kode_pegawai ?? '-'
                            )
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Pilih meja dan pegawai...'),

            // KW - Readonly (tidak bisa diedit)
            Select::make('kw')
                ->label('KW (dari Modal Repair)')
                ->required()
                ->disabled() // ← Tidak bisa diedit
                ->dehydrated() // ← PENTING! Tetap save ke database meski disabled
                ->helperText('KW otomatis sesuai dengan modal kayu yang dipilih')
                ->placeholder('Pilih modal repair terlebih dahulu...'),
        ]);
    }
}