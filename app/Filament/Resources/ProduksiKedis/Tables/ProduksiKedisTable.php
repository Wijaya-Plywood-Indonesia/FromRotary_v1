<?php

namespace App\Filament\Resources\ProduksiKedis\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Tables\Grouping\Group;

class ProduksiKedisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('kode_kedi')
                    ->label('Kedi')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('validasiKedi')
                    ->label('Validasi')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->validasiKedi()->exists()
                            ? 'Sudah divalidasi'
                            : 'Belum divalidasi';
                    })
                    ->color(function ($record) {
                        return $record->validasiKedi()->exists()
                            ? 'success'   // hijau bawaan Filament
                            : 'danger';    // merah bawaan Filament
                    }),
                TextColumn::make('kendala')
                    ->label('Kendala Produksi')
                    ->getStateUsing(
                        fn($record) =>
                        ($record->getRawOriginal('kendala') === null || $record->getRawOriginal('kendala') === '')
                        ? 'Tidak ada kendala'
                        : $record->getRawOriginal('kendala')
                    ),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('kelola_kendala')
                    ->label(fn($record) => $record->kendala ? 'Perbarui Kendala' : 'Tambah Kendala')
                    ->icon(fn($record) => $record->kendala ? 'heroicon-o-pencil-square' : 'heroicon-o-plus')
                    ->color(fn($record) => $record->kendala ? 'info' : 'warning')

                    // ✅ Form style baru di Filament 4
                    ->schema([
                        Textarea::make('kendala')
                            ->label('Kendala')
                            ->required()
                            ->rows(4),
                    ])

                    // ✅ Saat modal dibuka — isi form dengan data kendala lama jika ada
                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'kendala' => $record->kendala ?? '',
                        ]);
                    })

                    // ✅ Saat tombol Simpan ditekan
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'kendala' => trim($data['kendala']),
                        ]);

                        Notification::make()
                            ->title($record->kendala ? 'Kendala diperbarui' : 'Kendala ditambahkan')
                            ->success()
                            ->send();
                    })

                    ->modalHeading(fn($record) => $record->kendala ? 'Perbarui Kendala' : 'Tambah Kendala')
                    ->modalSubmitActionLabel('Simpan'),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groups(
                [
                    Group::make('status')
                        ->label('Status')
                        ->collapsible()
                        ->getTitleFromRecordUsing(function ($record) {
                            return ucfirst($record->status); // hasil: Masuk / Bongkar
                        }),
                ]
            )
            ->defaultGroup('status')
            ->groupingSettingsHidden()
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
