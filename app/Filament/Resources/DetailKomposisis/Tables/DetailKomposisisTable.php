<?php

namespace App\Filament\Resources\DetailKomposisis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

// Models
use App\Models\Komposisi;
use App\Models\BarangSetengahJadiHp;

class DetailKomposisisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ======================
                // 🔥 Kolom Komposisi
                // ======================
                TextColumn::make('komposisi_detail')
                    ->label('Komposisi')
                    ->getStateUsing(function ($record) {
                        if (!$record->komposisi || !$record->komposisi->barangSetengahJadiHp) {
                            return '—';
                        }

                        $bsj = $record->komposisi->barangSetengahJadiHp;

                        $kategori = $bsj->grade?->kategoriBarang?->nama_kategori ?? '-';
                        $ukuran   = $bsj->ukuran?->nama_ukuran ?? '-';
                        $grade    = $bsj->grade?->nama_grade ?? '-';
                        $jenis    = $bsj->jenisBarang?->nama_jenis_barang ?? '-';

                        return "{$kategori} | {$ukuran} | {$jenis} | {$grade}";
                    })
                    ->searchable()
                    ->sortable(),

                // ======================
                // Barang Setengah Jadi
                // ======================
                TextColumn::make('barang_setengah_jadi_hp_detail')
                    ->label('Barang Setengah Jadi')
                    ->getStateUsing(function ($record) {
                        $bsj = $record->barangSetengahJadiHp;

                        if (!$bsj) {
                            return '—';
                        }

                        $kategori = $bsj->grade?->kategoriBarang?->nama_kategori ?? '-';
                        $ukuran   = $bsj->ukuran?->nama_ukuran ?? '-';
                        $grade    = $bsj->grade?->nama_grade ?? '-';
                        $jenis    = $bsj->jenisBarang?->nama_jenis_barang ?? '-';

                        return "{$kategori} | {$ukuran} | {$grade} | {$jenis}";
                    })
                    ->sortable()
                    ->searchable(),

                // ======================
                // Lapisan
                // ======================
                TextColumn::make('lapisan')
                    ->label('Lapisan')
                    ->sortable()
                    ->numeric(),

                // ======================
                // Keterangan
                // ======================
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->searchable(),
            ])

            // ======================
            // FILTER
            // ======================
            ->filters([
                SelectFilter::make('id_komposisi')
                    ->label('Filter Komposisi')
                    ->options(function () {
                        return Komposisi::with(['barangSetengahJadiHp.ukuran', 'barangSetengahJadiHp.grade', 'barangSetengahJadiHp.jenisBarang'])
                            ->get()
                            ->mapWithKeys(function ($k) {
                                $bsj = $k->barangSetengahJadiHp;

                                $kategori = $bsj->grade?->kategoriBarang?->nama_kategori ?? '-';
                                $ukuran   = $bsj->ukuran?->nama_ukuran ?? '-';
                                $grade    = $bsj->grade?->nama_grade ?? '-';
                                $jenis    = $bsj->jenisBarang?->nama_jenis_barang ?? '-';

                                return [
                                    $k->id => "{$kategori} | {$ukuran} | {$grade} | {$jenis}"
                                ];
                            });
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('id_barang_setengah_jadi_hp')
                    ->label('Filter Barang Jadi')
                    ->relationship('barangSetengahJadiHp', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $kategori = $record->grade?->kategoriBarang?->nama_kategori ?? '-';
                        $ukuran   = $record->ukuran?->nama_ukuran ?? '-';
                        $grade    = $record->grade?->nama_grade ?? '-';
                        $jenis    = $record->jenisBarang?->nama_jenis_barang ?? '-';

                        return "{$kategori} | {$ukuran} | {$grade} | {$jenis}";
                    })
                    ->searchable()
                    ->preload(),
            ])

            // ======================
            // ACTIONS
            // ======================
            ->actions([
                EditAction::make(),
            ])

            // ======================
            // BULK
            // ======================
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
