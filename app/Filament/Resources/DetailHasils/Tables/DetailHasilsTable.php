<?php

namespace App\Filament\Resources\DetailHasils\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use App\Models\DetailHasil;

class DetailHasilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(DetailHasil::query())
            ->columns([
                TextColumn::make('no_palet')
                    ->label('No. Palet')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('kw')
                    ->label('KW')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('isi')
                    ->label('Isi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kayuMasuk.seri')
                    ->label('Seri Kayu')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('jenisKayu.nama_kayu')
                    ->label('Jenis Kayu')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->formatStateUsing(fn(string $state): string => $state),

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
                    ->relationship('jenisKayu', 'nama_kayu')
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
