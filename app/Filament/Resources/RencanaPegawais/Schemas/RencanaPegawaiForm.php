<?php

namespace App\Filament\Resources\RencanaPegawais\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema; // PAKAI Form, bukan Schema!
use App\Models\Pegawai;
use App\Models\RencanaPegawai;

class RencanaPegawaiForm
{
    public static function configure(Schema $form, $record = null): Schema
    {
        // Ambil ID produksi dari owner (RelationManager) atau dari record
        $produksiId = $record?->id_produksi_repair
            ?? request()->query('produksi_id')
            ?? $form->getLivewire()->ownerRecord?->id
            ?? request()->route('record');

        // NOMOR MEJA TERAKHIR → otomatis +1
        $lastMeja = RencanaPegawai::where('id_produksi_repair', $produksiId)->max('nomor_meja');

        // PEGAWAI YANG SUDAH DITUGASKAN → HILANG DARI DROPDOWN!
        $usedPegawaiIds = RencanaPegawai::where('id_produksi_repair', $produksiId)
            ->when($record, fn($q) => $q->where('id', '!=', $record->id))
            ->pluck('id_pegawai')
            ->toArray();

        return $form->schema([

            TimePicker::make('jam_masuk')
                ->label('Jam Masuk')
                ->default('06:00')
                ->seconds(false)
                ->required(),

            TimePicker::make('jam_pulang')
                ->label('Jam Pulang')
                ->default('17:00')
                ->seconds(false)
                ->required(),

            Select::make('id_pegawai')
                ->label('Pegawai')
                ->options(function () use ($usedPegawaiIds) {
                    return Pegawai::whereNotIn('id', $usedPegawaiIds)
                        ->orderBy('kode_pegawai')
                        ->get()
                        ->mapWithKeys(fn($p) => [
                            $p->id => "{$p->kode_pegawai} - {$p->nama_pegawai}"
                        ])
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Pilih pegawai...')
                ->reactive()
                ->rules([
                    fn() => function ($attribute, $value, $fail) use ($usedPegawaiIds) {
                        if (in_array($value, $usedPegawaiIds)) {
                            $fail('Pegawai ini sudah ditugaskan hari ini!');
                        }
                    }
                ]),

            TextInput::make('nomor_meja')
                ->label('Nomor Meja')
                ->numeric()
                ->minValue(1)
                ->default($lastMeja)
                ->required(),
        ]);
    }
}