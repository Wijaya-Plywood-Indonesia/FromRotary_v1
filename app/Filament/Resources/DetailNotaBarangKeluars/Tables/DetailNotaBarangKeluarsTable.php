<?php

namespace App\Filament\Resources\DetailNotaBarangKeluars\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailNotaBarangKeluarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nota.no_nota')
                    ->label('No Nota')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable()
                    ->numeric(),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->keterangan),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),

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
                DeleteBulkAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
