<?php

namespace App\Filament\Resources\PegawaiSandings\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PegawaiSandingsTable
{

    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tugas')
                    ->searchable(),
                TextColumn::make('masuk')
                    ->time()
                    ->sortable(),
                TextColumn::make('pulang')
                    ->time()
                    ->sortable(),
                TextColumn::make('ijin')
                    ->wrap()
                    ->label('Izin')
                    ->placeholder('Tidak Ada Izin')
                    ->searchable(),
                TextColumn::make('ket')
                    ->wrap()
                    ->placeholder('Tidak Ada Ket')
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
                CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->recordActions([


                // ➕ Tambah / Edit Ijin & Keterangan
                Action::make('aturIjin')
                    ->label(fn($record) => $record->ijin ? 'Edit Izin & Ket' : '+ Izin & Ket')
                    ->icon('heroicon-o-pencil-square')
                    ->color(fn($record) => $record->ijin ? 'danger' : 'kuning-loh')
                    ->form([
                        TextInput::make('ijin')->label('Izin'),
                        Textarea::make('ket')->label('Keterangan'),
                    ])
                    ->fillForm(fn($record) => [
                        'ijin' => $record->ijin,
                        'ket' => $record->ket,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update($data);
                    })
                    ->hidden(
                        fn($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
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
