<?php

namespace App\Filament\Resources\DetailMesins\Tables;

use App\Models\DetailMesin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
class DetailMesinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(DetailMesin::query())
            ->columns([
                // MESIN DRYER
                TextColumn::make('mesinDryer.nama_mesin')
                    ->label('Nama Mesin')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // JAM KERJA
                TextColumn::make('jam_kerja_mesin')
                    ->label('Jam Kerja')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                // PRODUKSI DRYER - TANGGAL
                TextColumn::make('produksiDryer.tanggal_produksi')
                    ->label('Tanggal Produksi')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                // PRODUKSI DRYER - SHIFT
                TextColumn::make('produksiDryer.shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn($state) => $state === 'PAGI' ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => $state),

                // DIBUAT
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

                SelectFilter::make('id_mesin_dryer')
                    ->label('Mesin')
                    ->relationship('mesinDryer', 'nama_mesin')
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
