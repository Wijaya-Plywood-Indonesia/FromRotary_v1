<?php

namespace App\Filament\Resources\DetailNotaBarangMasuks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailNotaBarangMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_nota_bm')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nama_barang')
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('satuan')
                    ->searchable(),
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
                CreateAction::make()->label('Tambah Barang'),

                Action::make('validasi_nota')
                    ->label('Validasi Nota')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn(RelationManager $livewire) =>
                        $livewire->ownerRecord->divalidasi_oleh === null
                    )
                    ->disabled(
                        fn(RelationManager $livewire) =>
                        $livewire->ownerRecord->dibuat_oleh === auth()->id()
                    )
                    ->action(function (RelationManager $livewire) {

                        $nota = $livewire->ownerRecord;

                        $nota->update([
                            'divalidasi_oleh' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Nota berhasil divalidasi!')
                            ->success()
                            ->send();
                    })
                    ->after(function ($livewire) {
                        // Refresh komponen supaya status berubah di tabel
                        $livewire->dispatch('$refresh');
                    }),
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
