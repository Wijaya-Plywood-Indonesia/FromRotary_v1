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
                        $grade = match ($record->grade) {
                            1 => 'A',
                            2 => 'B',
                            default => '-',
                        };

                        return "{$namaKayu} {$panjang} ({$grade})";
                    })
                    ->sortable(['jenisKayu.nama_kayu', 'panjang', 'grade']) // tetap bisa diurutkan
                    ->searchable(['jenisKayu.nama_kayu', 'panjang']) // bisa dicari juga
                    //  ->badge()
                    ->color(fn($record) => match ($record->grade) {
                        1 => 'success',
                        2 => 'primary',
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
