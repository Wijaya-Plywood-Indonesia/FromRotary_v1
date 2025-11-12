<?php

namespace App\Filament\Resources\DetailMasuks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use App\Models\DetailMasuk;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class DetailMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(DetailMasuk::query())
            ->columns([
                // NO PALET
                TextColumn::make('no_palet')
                    ->label('No. Palet')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                // KW
                TextColumn::make('kw')
                    ->label('KW')
                    ->searchable()
                    ->sortable(),

                // ISI
                TextColumn::make('isi')
                    ->label('Isi')
                    ->searchable()
                    ->sortable(),

                // KAYU MASUK (RELASI)
                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri Kayu')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // JENIS KAYU (RELASI)
                TextColumn::make('jenisKayu.nama_jenis')
                    ->label('Jenis Kayu')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // PRODUKSI DRYER (RELASI)
                TextColumn::make('produksiDryer.tanggal_produksi')
                    ->label('Tanggal Produksi')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('produksiDryer.shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PAGI' => 'success',
                        'MALAM' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => $state === 'PAGI' ? 'PAGI' : 'MALAM'),

                // TANGGAL DIBUAT
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_produksi_dryer')
                    ->label('Produksi Dryer')
                    ->relationship('produksiDryer', 'tanggal_produksi')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->tanggal_produksi->format('d M Y') . ' | ' . $record->shift
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->relationship('jenisKayu', 'nama_jenis')
                    ->searchable()
                    ->preload(),
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
