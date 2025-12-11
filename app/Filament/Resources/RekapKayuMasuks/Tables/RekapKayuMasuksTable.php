<?php

namespace App\Filament\Resources\RekapKayuMasuks\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RekapKayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ===========================
                // KOLOM ASLI DB (AMAN SORT+SEARCH)
                // ===========================

                TextColumn::make('tgl_kayu_masuk')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('penggunaanSupplier.nama_supplier')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('seri')
                    ->label('Seri')
                    ->sortable()
                    ->searchable(),

                // ===========================
                // KOLOM RELASI / HASIL OLAHAN
                // SORT & SEARCH MANUAL
                // ===========================

                TextColumn::make('panjang')
                    ->label('Panjang')
                    ->wrap()
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailMasukanKayu
                            ->pluck('panjang')
                            ->unique()
                            ->implode(', ')
                        ?? '-'
                    ),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->wrap()
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailMasukanKayu
                            ->pluck('jenisKayu.nama_kayu')
                            ->unique()
                            ->implode(', ')
                        ?? '-'
                    )
                    ->searchable(
                        query: fn(Builder $query, string $search) =>
                        $query->whereHas(
                            'detailMasukanKayu.jenisKayu',
                            fn($q) =>
                            $q->where('nama_kayu', 'like', "%{$search}%")
                        )
                    ),

                TextColumn::make('lahan')
                    ->label('Lahan')
                    ->wrap()
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailMasukanKayu
                            ->pluck('lahan.kode_lahan')
                            ->unique()
                            ->implode(', ')
                        ?? '-'
                    )
                    ->searchable(
                        query: fn(Builder $query, string $search) =>
                        $query->whereHas(
                            'detailMasukanKayu.lahan',
                            fn($q) =>
                            $q->where('kode_lahan', 'like', "%{$search}%")
                        )
                    ),

                // ===========================
                // TOTAL BATANG (SUM)
                // ===========================

                TextColumn::make('banyak')
                    ->label('Total Batang')
                    ->numeric()
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailMasukanKayu->sum('jumlah_batang') ?? 0
                    ),

                TextColumn::make('diameter')
                    ->label('Diameter')
                    ->wrap()
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailMasukanKayu
                            ->pluck('diameter')
                            ->unique()
                            ->implode(', ')
                        ?? '-'
                    ),

                // ===========================
                // STATUS NOTA (BADGE)
                // ===========================

                BadgeColumn::make('status')
                    ->label('Status Nota')
                    ->tooltip(
                        fn($record) =>
                        $record->notaKayu->first()?->status ?? 'Belum Diperiksa'
                    )
                    ->getStateUsing(function ($record) {

                        $status = $record->notaKayu->first()?->status;

                        if ($status && str_contains(strtolower($status), 'sudah')) {
                            return 'Sudah Cetak Nota';
                        }

                        return 'Belum Cetak Nota';
                    })
                    ->colors([
                        'success' => 'Sudah Cetak Nota',
                        'danger' => 'Belum Cetak Nota',
                    ])
                    ->icon(fn(string $state) => match ($state) {
                        'Sudah Cetak Nota' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle',
                    })
                    ->searchable(
                        query: fn(Builder $query, string $search) =>
                        $query->whereHas(
                            'notaKayu',
                            fn($q) =>
                            $q->where('status', 'like', "%{$search}%")
                        )
                    ),

            ])
            ->defaultSort('seri', 'desc');
    }
}
