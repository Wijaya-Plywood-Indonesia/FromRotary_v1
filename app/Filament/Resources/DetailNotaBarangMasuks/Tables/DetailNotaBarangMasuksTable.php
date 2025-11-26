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
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()->label('Tambah Barang'),


              Action::make('validasi_nota')
    ->label('Validasi Nota')
    ->icon('heroicon-o-check')
    ->color('success')
    ->requiresConfirmation()
    ->visible(function (RelationManager $livewire) {
        // Tombol hanya muncul jika BELUM divalidasi
        return empty($livewire->ownerRecord->divalidasi_oleh);
    })
    ->disabled(function (RelationManager $livewire) {
        // Pembuat TIDAK boleh validasi
        return $livewire->ownerRecord->dibuat_oleh == auth()->id();
    })
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
        // Refresh komponen supaya status berubah
        $livewire->dispatch('$refresh');
    }),
                Action::make('batalkan_validasi')
                    ->label('Batalkan Validasi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (RelationManager $livewire) {
                        $nota = $livewire->ownerRecord;

                        // Tombol muncul hanya jika nota SUDAH divalidasi
                        return $nota->divalidasi_oleh != null;
                    })
                    ->action(function (RelationManager $livewire) {
                        $nota = $livewire->ownerRecord;

                        $nota->update([
                            'divalidasi_oleh' => null,
                        ]);

                        Notification::make()
                            ->title('Validasi berhasil dibatalkan.')
                            ->danger()
                            ->send();
                    })
                    ->after(fn($livewire) => $livewire->dispatch('$refresh')),


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
