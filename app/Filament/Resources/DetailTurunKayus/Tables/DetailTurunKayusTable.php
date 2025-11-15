<?php

namespace App\Filament\Resources\DetailTurunKayus\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\DetailTurunKayu;
use Illuminate\Support\Facades\Storage;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class DetailTurunKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                DetailTurunKayu::query()
                    ->with([
                        'pegawaiTurunKayu.pegawai',
                        'kayuMasuk.penggunaanSupplier',
                        'kayuMasuk.penggunaanKendaraanSupplier'
                    ])
            )
            ->columns([  // BENAR: columns(), BUKAN components()

                // 1. PEKERJA
                TextColumn::make('pegawai')
                    ->label('Pekerja')
                    ->getStateUsing(function ($record) {
                        $pegawais = $record->pegawaiTurunKayu->pluck('pegawai')->filter();

                        if ($pegawais->isEmpty()) {
                            return '—';
                        }

                        return $pegawais->map(function ($pegawai) {
                            return $pegawai->kode_pegawai . ' - ' . $pegawai->nama_pegawai;
                        })->implode(', ');
                    })
                    ->badge()
                    ->searchable(
                        query: fn($query, $search) => $query->whereHas(
                            'pegawaiTurunKayu.pegawai',
                            fn($q) => $q
                                ->where('nama_pegawai', 'like', "%{$search}%")
                                ->orWhere('kode_pegawai', 'like', "%{$search}%")
                        )
                    )
                    ->sortable(
                        query: fn($query, $direction) => $query
                            ->join('pegawai_turun_kayus', 'detail_turun_kayus.id', '=', 'pegawai_turun_kayus.id_detail_turun_kayu')
                            ->join('pegawais', 'pegawai_turun_kayus.id_pegawai', '=', 'pegawais.id')
                            ->orderBy('pegawais.nama_pegawai', $direction)
                            ->select('detail_turun_kayus.*')
                    ),
                // 2. SUPPLIER
                TextColumn::make('kayuMasuk.penggunaanSupplier.nama_supplier')
                    ->label('Supplier')
                    ->getStateUsing(
                        fn($record) =>
                        $record?->kayuMasuk?->penggunaanSupplier?->nama_supplier ?? '—'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_supir')
                    ->label('Nama Supir')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_kayu')
                    ->label('Jumlah Kayu')
                    ->searchable()
                    ->sortable(),

                // 3. NOPOL + JENIS
                TextColumn::make('kayuMasuk.penggunaanKendaraanSupplier.nopol_kendaraan')
                    ->label('Nopol & Jenis')
                    ->getStateUsing(
                        fn($record) =>
                        $record?->kayuMasuk?->penggunaanKendaraanSupplier
                        ? "{$record->kayuMasuk->penggunaanKendaraanSupplier->nopol_kendaraan} ({$record->kayuMasuk->penggunaanKendaraanSupplier->jenis_kendaraan})"
                        : '—'
                    )
                    ->searchable()
                    ->sortable(),

                // 4. SERI
                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri')
                    ->getStateUsing(fn($record) => $record?->kayuMasuk?->seri ?? '—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'selesai' => 'success',
                        'proses' => 'warning',
                        'menunggu' => 'gray',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('foto')
                    ->label('Foto')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Ada File' : 'Kosong')
                    ->color(fn($state) => $state ? 'success' : 'danger')

                    // --- TAMBAHAN ---
                    ->url(function (?string $state): ?string {
                        // Jika state (nama file) ada, buat URL
                        if ($state) {
                            return Storage::url($state);
                        }
                        // Jika tidak, jangan berikan URL (badge tidak bisa diklik)
                        return null;
                    })
                    ->openUrlInNewTab(), // Buka di tab baru

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada detail')
            ->emptyStateDescription('Tambahkan pekerja dan kayu masuk.')
            ->defaultSort('created_at', 'desc');
    }
}