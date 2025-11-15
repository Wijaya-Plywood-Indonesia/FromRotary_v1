<?php

namespace App\Filament\Resources\DetailMesins\Tables;

use App\Models\DetailMesin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
class DetailMesinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mesin.nama_mesin') // Asumsi: relasi 'mesinDryer' & kolom 'nama'
                    ->label('Mesin Dryer')
                    ->searchable()
                    ->placeholder('N/A'), // Teks jika mesin tidak dipilih (nullable)

                TextColumn::make('jam_kerja_mesin')
                    ->label('Jam Kerja Mesin')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan by default
            ])
            ->filters([
                // Tempat filter jika Anda membutuhkannya
            ])
            ->headerActions([
                // INI ADALAH TOMBOL UNTUK MEMBUAT DATA BARU
                CreateAction::make(),
            ])
            ->recordActions([
                // Tombol di setiap baris (Edit, Delete)
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
