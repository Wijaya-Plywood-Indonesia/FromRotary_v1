<?php

namespace App\Filament\Resources\DetailDempuls\Schemas;

use App\Models\BarangSetengahJadiHp;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\RencanaPegawaiDempul;
use App\Models\Grade;
use App\Models\JenisBarang;

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
                                    $item->id => sprintf('%s - %s', $kode, $nama)
                                ];
                            });
                    })
                    // Fitur menyimpan pilihan terakhir di session (sesuai kode asli Anda)
                    ->afterStateUpdated(fn($state) => session(['detail_last_rencana_pegawai' => $state]))
                    ->default(fn() => session('detail_last_rencana_pegawai'))
                    ->columnSpanFull(),
                Select::make('grade_id')
                    ->label('Grade')
                    ->options(
                        Grade::with('kategoriBarang')
                            ->orderBy('id_kategori_barang')
                            ->orderBy('nama_grade')
                            ->get()
                            ->mapWithKeys(fn($g) => [
                                $g->id => ($g->kategoriBarang?->nama_kategori ?? 'Tanpa Kategori')
                                    . ' | ' . $g->nama_grade
                            ])
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Grade')
                    ->dehydrated(false),

                /*
                |--------------------------------------------------------------------------
                | FILTER JENIS BARANG
                |--------------------------------------------------------------------------
                */
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(
                        JenisBarang::orderBy('nama_jenis_barang')
                            ->pluck('nama_jenis_barang', 'id')
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Jenis Barang')
                    ->dehydrated(false),

                /*
                |--------------------------------------------------------------------------
                | BARANG SETENGAH JADI (HASIL FILTER)
                |--------------------------------------------------------------------------
                */
                Select::make('id_barang_setengah_jadi_hp')
                    ->label('Barang Setengah Jadi')
                    ->required()
                    ->searchable()
                    ->options(function (callable $get) {

                        $query = BarangSetengahJadiHp::query()
                            ->with([
                                'ukuran',
                                'jenisBarang',
                                'grade.kategoriBarang',
                            ])
                            // ✅ AMAN: pakai relasi, bukan nama tabel
                            ->joinRelationship('jenisBarang')
                            ->joinRelationship('ukuran');

                        // FILTER GRADE
                        if ($get('grade_id')) {
                            $query->where('barang_setengah_jadi_hp.id_grade', $get('grade_id'));
                        }

                        // FILTER JENIS BARANG (opsional)
                        if ($get('jenis_barang_id')) {
                            $query->where('barang_setengah_jadi_hp.id_jenis_barang', $get('jenis_barang_id'));
                        }

                        // 🔥 URUTAN SESUAI KEINGINAN
                        $query
                            ->orderBy('jenis_barang.nama_jenis_barang', 'asc') // Meranti → Sengon
                            ->orderBy('ukurans.tebal', 'asc')                  // 3 → 4 → 5
                            ->orderBy('barang_setengah_jadi_hp.id', 'asc');

                        return $query
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(function ($b) {

                                $kategori = $b->grade?->kategoriBarang?->nama_kategori ?? '-';
                                $ukuran = $b->ukuran?->nama_ukuran ?? '-';
                                $grade = $b->grade?->nama_grade ?? '-';
                                $jenis = $b->jenisBarang?->nama_jenis_barang ?? '-';

                                return [
                                    $b->id => "{$kategori} | {$ukuran} | {$grade} | {$jenis}"
                                ];
                            });
                    })
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
