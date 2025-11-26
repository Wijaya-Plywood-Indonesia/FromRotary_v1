<?php

namespace App\Filament\Resources\KayuMasuks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis_dokumen_angkut')
                    ->searchable(),
                TextColumn::make('penggunaanSupplier.nama_supplier')
                    ->label('Nama Supplier')
                //->badge()
                //  ->color('success')
                ,
                
                TextColumn::make('upload_dokumen_angkut')
                    ->label('Dokumen Legal')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Ada File' : 'Kosong')
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                TextColumn::make('tgl_kayu_masuk')->dateTime()->sortable(),
                
                TextColumn::make('seri')->numeric()->sortable(),
                
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                
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
            ->defaultSort('created_at', 'desc')
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
