<?php

namespace App\Filament\Resources\DetailPegawaiHps\Schemas;

use Filament\Schemas\Schema;
use App\Models\Pegawai;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

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

            // --- ID PEGAWAI (Relation: pegawai) ---
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

            // --- TUGAS (Textarea) ---
            Textinput::make('tugas')
                ->label('Tugas')
                ->maxLength(500)
                ->required(),

            // --- JAM MASUK (Select dengan Options khusus) ---
            Select::make('masuk')
                ->label('Jam Masuk')
                ->options(self::timeOptions())
                ->default('08:00')
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
            
            // --- IJIN (TextInput) ---
            TextInput::make('ijin')
                ->label('Izin')
                ->nullable()
                ->maxLength(255),

            // --- KETERANGAN (Textarea) ---
            Textarea::make('ket')
                ->label('Keterangan')
                ->rows(1)
                ->nullable()
                ->maxLength(255),
        ]);
    }
}
