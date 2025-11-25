<?php

namespace App\Filament\Resources\DetailTurusanKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailTurusanKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomer_urut')
                    ->label('No')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('kayu_masuk_id')
                    ->label('Kayu Masuk')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('lahan_display')
                    ->label('Lahan')
                    ->getStateUsing(fn($record) => "{$record->lahan->kode_lahan}")
                    ->sortable(['lahan.kode_lahan'])
                    ->searchable(['lahan.kode_lahan']),
                TextColumn::make('keterangan_kayu')
                    ->label('Kayu')
                    ->getStateUsing(function ($record) {
                        $namaKayu = $record->jenisKayu?->nama_kayu ?? '-';
                        $panjang = $record->panjang ?? '-';

                        // Normalisasi nilai agar aman di server
                        $rawGrade = $record->grade;

                        // buang spasi + uppercase
                        $clean = trim(strtoupper((string) $rawGrade));

                        // konversi ke integer kalau isi angka
                        $gradeInt = is_numeric($clean) ? intval($clean) : $clean;

                        // match fleksibel: terima 1, "1", "A", "a"
                        $grade = match ($gradeInt) {
                            1, '1', 'A' => 'A',
                            2, '2', 'B' => 'B',
                            default => '-',
                        };

                        return "{$namaKayu} {$panjang} ({$grade})";
                    })
                    ->sortable(['jenisKayu.nama_kayu', 'panjang', 'grade'])
                    ->searchable(['jenisKayu.nama_kayu', 'panjang'])
                    ->color(fn($record) => match (trim((string) $record->grade)) {
                        '1', 'A' => 'success',
                        '2', 'B' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('diameter')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kuantitas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('updatedBy.name')
                    ->label('Diubah Oleh')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
