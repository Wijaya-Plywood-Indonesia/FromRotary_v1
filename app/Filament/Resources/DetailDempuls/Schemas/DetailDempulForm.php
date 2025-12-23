<?php

namespace App\Filament\Resources\DetailDempuls\Schemas;

use App\Models\BarangSetengahJadiHp;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\RencanaPegawaiDempul;

class DetailDempulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_rencana_pegawai_dempul')
                    ->label('Pegawai Dempul')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return RencanaPegawaiDempul::query()
                            // Kita load relasi 'pegawai' agar tidak berat (N+1 problem)
                            ->with(['pegawai'])
                            ->latest() // Opsional: urutkan dari yang terbaru
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                // Ambil data dari relasi pegawai
                                $kode = $item->pegawai?->kode_pegawai ?? '???';
                                $nama = $item->pegawai?->nama_pegawai ?? 'Tanpa Nama';
                                return [
                                    $item->id => sprintf('[%s] %s', $kode, $nama)
                                ];
                            });
                    })
                    // Fitur menyimpan pilihan terakhir di session (sesuai kode asli Anda)
                    ->afterStateUpdated(fn($state) => session(['detail_last_rencana_pegawai' => $state]))
                    ->default(fn() => session('detail_last_rencana_pegawai'))
                    ->columnSpanFull(),
                Select::make('id_barang_setengah_jadi_hp')
                    ->label('Bahan Dempul')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return BarangSetengahJadiHp::query()
                            ->with([
                                'ukuran',
                                'jenisBarang',
                                'grade.kategoriBarang'
                            ])
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(function ($item) {

                                $kategori = $item->grade?->kategoriBarang?->nama_kategori ?? 'Kategori?';
                                $ukuran = $item->ukuran?->nama_ukuran ?? 'Ukuran?';
                                $jenis = $item->jenisBarang?->nama_jenis_barang ?? 'Jenis?';
                                $grade = $item->grade?->nama_grade ?? 'Grade?';

                                return [
                                    $item->id => sprintf(
                                        '%s | %s | %s | %s',
                                        $kategori,
                                        $ukuran,
                                        $jenis,
                                        $grade
                                    )
                                ];
                            });
                    })
                    ->afterStateUpdated(fn($state) => session(['detail_last_bahan_dempul' => $state]))
                    ->default(fn() => session('detail_last_bahan_dempul'))
                    ->columnSpanFull(),
                TextInput::make('modal')
                    ->label('Modal dempul')
                    ->placeholder('Masukkan jumlah dempul')
                    ->required()
                    ->numeric(),
                TextInput::make('hasil')
                    ->label('Hasil dempul')
                    ->placeholder('Masukkan hasil dempul')
                    ->required()
                    ->numeric(),
                TextInput::make('nomor_palet')
                    ->label('Nomor Palet')
                    ->numeric(),
            ]);
    }
}
