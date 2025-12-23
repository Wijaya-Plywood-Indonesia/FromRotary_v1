<?php

namespace App\Filament\Resources\DetailDempuls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Grouping\Group;

class DetailDempulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('id_rencana_pegawai_dempul')
                    ->label('Pegawai')
                    ->getTitleFromRecordUsing(function ($record) {
                        $pegawai = $record->rencanaPegawaiDempul?->pegawai;

                        if (!$pegawai) {
                            return 'Pegawai Tidak Diketahui';
                        }
                        return "[{$pegawai->kode_pegawai}] {$pegawai->nama_pegawai}";
                    })
                    ->collapsible(),
            ])

            ->columns([
                TextColumn::make('id_barang_setengah_jadi_hp')
                    ->label('Bahan (Veneer)')
                    ->getStateUsing(function ($record) {
                        $bsj = $record->barangSetengahJadi;

                        if (!$bsj) {
                            return '—';
                        }

                        $ukuran = $bsj->ukuran?->nama_ukuran ?? '-';
                        $grade = $bsj->grade?->nama_grade ?? '-';
                        $jenis = $bsj->jenisBarang?->nama_jenis_barang ?? '-';

                        return "{$jenis} | {$ukuran} | {$grade}";
                    })
                    ->searchable(),
                TextColumn::make('modal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hasil')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nomor_palet')
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
            ->filters([
                //
            ])
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
            ]);
    }
}
