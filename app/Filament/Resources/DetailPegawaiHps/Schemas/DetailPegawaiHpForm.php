<?php

namespace App\Filament\Resources\DetailPegawaiHps\Schemas;

use Filament\Schemas\Schema;
use App\Models\Pegawai;
use App\Models\Mesin;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\Select;

class DetailPegawaiHpForm
{
    // Fungsi untuk menghasilkan opsi jam setiap jam (00:00 hingga 23:00)
    public static function timeOptions(): array
    {
        // Menggunakan interval 1 jam
        return collect(CarbonPeriod::create('00:00', '1 hour', '23:00')->toArray())
            ->mapWithKeys(fn($time) => [
                $time->format('H:i') => $time->format('H.i'),
            ])
            ->toArray();
    }
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // --- JAM MASUK (Select dengan Options khusus) ---
            Select::make('masuk')
                ->label('Jam Masuk')
                ->options(self::timeOptions())
                ->default('06:00')
                ->required()
                ->searchable()
                // Menyimpan ke DB sebagai 'HH:MM:00'
                ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                // Menampilkan di form hanya 'HH:MM'
                ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null),

            // --- JAM PULANG (Select dengan Options khusus) ---
            Select::make('pulang')
                ->label('Jam Pulang')
                ->options(self::timeOptions())
                ->default('17:00')
                ->required()
                ->searchable()
                ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null),

            Select::make('tugas')
                ->label('Tugas')
                ->options([
                    'Operator_hp' => 'Operator HP',
                    'pilih_hp' => 'Pilih HP',
                    'nata_hp' => 'Nata HP',
                    'masak_lem' => 'Masak Lem',
                    'roll_glue' => 'Roll Glue',
                ])

                ->required()
                ->native(false)
                ->searchable(),

            Select::make('id_mesin')
                ->label('Mesin Hotpress')
                ->options(
                    Mesin::whereHas('kategoriMesin', function ($query) {
                        $query->where('nama_kategori_mesin', 'HOTPRESS');
                    })
                        ->orderBy('nama_mesin')
                        ->pluck('nama_mesin', 'id')
                )
                ->searchable()
                ->required(),

            // --- ID PEGAWAI (Relation: pegawai) ---
            Select::make('id_pegawai')
                ->label('Pegawai')
                ->options(
                    Pegawai::query()
                        ->orderBy('nama_pegawai')
                        ->get()
                        ->mapWithKeys(fn($pegawai) => [
                            $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                        ])
                )
                ->searchable()
                ->required(),


            // // --- IJIN (TextInput) ---
            // TextInput::make('ijin')
            //     ->label('Izin')
            //     ->nullable()
            //     ->maxLength(255),

            // // --- KETERANGAN (Textarea) ---
            // Textarea::make('ket')
            //     ->label('Keterangan')
            //     ->rows(1)
            //     ->nullable()
            //     ->maxLength(255),
        ]);
    }
}
