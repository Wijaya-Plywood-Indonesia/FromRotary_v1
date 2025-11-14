<?php

namespace App\Filament\Resources\DetailTurunKayus\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\DetailTurunKayu;

class DetailTurunKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                DetailTurunKayu::query()
                    ->with([
                        'pegawai',
                        'kayuMasuk',
                        'kayuMasuk.penggunaanSupplier',
                        'kayuMasuk.penggunaanKendaraanSupplier',
                        'turunKayu'
                    ])
            )
            ->columns([


                // PEGAWAI
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pegawai')
                    ->getStateUsing(fn($record) => $record?->pegawai?->pluck('nama_pegawai')->join(', ') ?? '—')
                    ->badge()
                    ->searchable(),

                // SUPPLIER
                TextColumn::make('kayuMasuk.penggunaanSupplier.nama_supplier')
                    ->label('Supplier')
                    ->getStateUsing(fn($record) => $record?->kayuMasuk?->penggunaanSupplier?->nama_supplier ?? '—')
                    ->placeholder('—')
                    ->searchable(),

                // NOPOL
                TextColumn::make('kayuMasuk.penggunaanKendaraanSupplier.nopol_kendaraan')
                    ->label('Nopol')
                    ->getStateUsing(fn($record) => $record?->kayuMasuk?->penggunaanKendaraanSupplier?->nopol_kendaraan ?? '—')
                    ->placeholder('—')
                    ->searchable(),

                // JENIS KENDARAAN
                TextColumn::make('kayuMasuk.penggunaanKendaraanSupplier.jenis_kendaraan')
                    ->label('Jenis Kendaraan')
                    ->getStateUsing(fn($record) => $record?->kayuMasuk?->penggunaanKendaraanSupplier?->jenis_kendaraan ?? '—')
                    ->placeholder('—'),

                // SERI
                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri')
                    ->getStateUsing(fn($record) => $record?->kayuMasuk?->seri ?? '—')
                    ->placeholder('—')
                    ->searchable(),

                // DIBUAT
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('id_turun_kayu')
                    ->relationship('turunKayu', 'tanggal')
                    ->getOptionLabelFromRecordUsing(fn($r) => $r?->tanggal?->format('d M Y') ?? '—'),

                SelectFilter::make('id_pegawai')
                    ->relationship('pegawai', 'nama_pegawai')
                    ->getOptionLabelFromRecordUsing(fn($p) => $p ? "{$p->kode_pegawai} - {$p->nama_pegawai}" : '—')
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('id_kayu_masuk')
                    ->relationship('kayuMasuk', 'seri')
                    ->getOptionLabelFromRecordUsing(
                        fn($k) =>
                        $k ? ($k->penggunaanSupplier?->nama_supplier ?? '') . ' | ' .
                        ($k->penggunaanKendaraanSupplier?->nopol_kendaraan ?? '') . ' | ' . $k->seri : '—'
                    )
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada data')
            ->emptyStateDescription('Silakan tambah detail turun kayu.')
            ->defaultSort('created_at', 'desc');
    }
}