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
                        'pegawai',
                        'kayuMasuk.penggunaanSupplier',
                        'kayuMasuk.penggunaanKendaraanSupplier'
                    ])
            )
            ->columns([  // BENAR: columns(), BUKAN components()

                // 1. PEKERJA
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pekerja')
                    ->formatStateUsing(function ($record) {
                        if (!$record->pegawai) {
                            return '—';
                        }

                        // Tampilkan kode + nama pegawai (hanya 1 pegawai per baris)
                        return $record->pegawai->kode_pegawai . ' - ' . $record->pegawai->nama_pegawai;
                    })
                    ->badge()
                    ->searchable()
                    ->sortable(),

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