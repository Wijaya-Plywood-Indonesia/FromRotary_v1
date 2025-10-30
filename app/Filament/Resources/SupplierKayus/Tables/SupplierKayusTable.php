<?php

namespace App\Filament\Resources\SupplierKayus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('status_supplier')
                    ->badge()
                    ->formatStateUsing(fn($state) => (string) $state === '0' ? 'Tidak Aktif' : 'Aktif')
                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'warning',
                    })
                ,

                TextColumn::make('nama_supplier')
                    ->searchable(),

                TextColumn::make('no_telepon')
                    ->icon('heroicon-o-phone')
                    ->searchable(),
                // TextColumn::make('nik')
                //     ->searchable(),

                TextColumn::make('jenis_kelamin_pegawai')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn($state) => (string) $state === '0' ? 'Perempuan' : 'Laki-laki')
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'primary',
                        '0' => 'pink',
                    }),

                TextColumn::make('jenis_bank')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_rekening')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),

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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
