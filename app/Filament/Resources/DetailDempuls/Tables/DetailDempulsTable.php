<?php

namespace App\Filament\Resources\DetailDempuls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

class DetailDempulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /**
             * ===========================================
             * 🚀 OPTIMASI QUERY (PENTING UNTUK GROUPING)
             * ===========================================
             * Kita harus men-load relasi 'pegawai' di sini agar
             * saat rendering judul Group tidak terjadi N+1 Query.
             */
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with([
                    'rencanaPegawaiDempul.pegawai',
                    // Load juga data untuk kolom agar ringan
                    'barangSetengahJadi.ukuran',
                    'barangSetengahJadi.jenisBarang',
                    'barangSetengahJadi.grade.kategoriBarang',
                ]);
            })

            /**
             * ======================
             * 🔥 GROUPING
             * ======================
             */
            ->groups([
                Group::make('id_rencana_pegawai_dempul')
                    ->label('Pegawai / Rencana')
                    ->getTitleFromRecordUsing(function ($record) {
                        // Ambil relasi ke atas (Rencana -> Pegawai)
                        $rencana = $record->rencanaPegawaiDempul;
                        $pegawai = $rencana?->pegawai;

                        if (!$pegawai) {
                            return 'Pegawai Tidak Diketahui';
                        }

                        // Format Judul Group: [KODE] Nama Pegawai | Tanggal (Opsional)
                        return sprintf(
                            '[%s] %s',
                            $pegawai->kode_pegawai,
                            $pegawai->nama_pegawai
                        );
                    })
                    ->collapsible(), // Bisa dilipat
            ])

            /**
             * ======================
             * 📋 COLUMNS
             * ======================
             */
            ->columns([
                TextColumn::make('id_barang_setengah_jadi_hp')
                    ->label('Bahan (Veneer)')
                    ->getStateUsing(function ($record) {
                        $bsj = $record->barangSetengahJadi;
                        if (!$bsj)
                            return '—';

                        $kategori = $bsj->grade?->kategoriBarang?->nama_kategori ?? '-';
                        $ukuran = $bsj->ukuran?->nama_ukuran ?? '-';
                        $grade = $bsj->grade?->nama_grade ?? '-';
                        $jenis = $bsj->jenisBarang?->nama_jenis_barang ?? '-';

                        return "{$kategori} | {$ukuran} | {$grade} | {$jenis}";
                    })
                    ->searchable(), // (Logic search perlu query builder khusus seperti sebelumnya jika ingin akurat)

                TextColumn::make('modal')
                    ->label('Modal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hasil')
                    ->label('Hasil')
                    ->numeric()
                    ->sortable()
                    // LOGIKA WARNA
                    ->color(fn($record) => $record->hasil < $record->modal ? 'danger' : 'success'),
                TextColumn::make('nomor_palet')
                    ->numeric()
                    ->sortable(),
            ])

            /**
             * ======================
             * ✏️ ACTIONS
             * ======================
             */
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            /**
             * ======================
             * ⚙️ DEFAULT GROUPING
             * ======================
             * Ini yang membuat tabel otomatis terkelompok saat dibuka
             */
            ->defaultGroup('id_rencana_pegawai_dempul');
    }
}