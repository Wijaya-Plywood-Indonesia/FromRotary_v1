<?php

namespace App\Filament\Resources\DetailKayuMasuks\Tables;

use App\Models\DetailKayuMasuk;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailKayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('diameter')
                    ->label('D')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('panjang')
                    ->label('Panjang')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('A / B')
                    ->formatStateUsing(fn($state) => match ($state) {
                        1 => 'Grade A',
                        2 => 'Grade B',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        1 => 'success', // hijau untuk Grade A
                        2 => 'primary', // kuning untuk Grade B
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('jumlah_batang')
                    ->numeric()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(), // ðŸ‘ˆ ini yang munculkan tombol "Tambah"
            ])
            ->recordActions([
                Action::make('kurangiBatang')
                    ->label('Kurangi Batang')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->button() // ✅ tampil sebagai tombol juga
                    ->outlined(false)
                    ->size('sm')
                    //  ->requiresConfirmation()
                    ->action(function (DetailKayuMasuk $record) {
                        if ($record->jumlah_batang > 0) {
                            $record->decrement('jumlah_batang');
                            $record->save();
                        }
                    }),
                Action::make('tambahBatang')
                    ->label('Tambah Batang')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->button() // ✅ ubah jadi tombol solid
                    ->outlined(false) // (opsional) kalau mau solid penuh
                    ->size('sm') // kecil, biar rapi
                    ->action(function (DetailKayuMasuk $record) {
                        $record->increment('jumlah_batang');
                        $record->save();
                    }),


                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
