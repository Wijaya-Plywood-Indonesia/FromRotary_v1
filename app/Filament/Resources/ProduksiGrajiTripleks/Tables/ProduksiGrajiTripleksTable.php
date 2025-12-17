<?php

namespace App\Filament\Resources\ProduksiGrajiTripleks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Tables\Table;

class ProduksiGrajiTripleksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_produksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
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

                // // BadgeColumn::make('validasiTerakhir.status')
                // //     ->label('Validasi')
                // //     ->colors([
                // //         'success' => 'divalidasi',
                // //         'warning' => 'ditangguhkan',
                // //         'danger' => 'ditolak',
                //     ])
                //     ->icons([
                //         'heroicon-o-check-circle' => 'divalidasi',
                //         'heroicon-o-x-circle' => 'ditolak',
                //         'heroicon-o-exclamation-circle' => 'ditangguhkan',
                //     ])
                //     ->sortable()
                //     ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal_produksi', 'desc')
            ->filters([
                Filter::make('tanggal_produksi')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->placeholder('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->placeholder('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_produksi', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_produksi', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('kelola_kendala')
                    ->label(fn($record) => $record->kendala ? 'Perbarui Kendala' : 'Tambah Kendala')
                    ->icon(fn($record) => $record->kendala ? 'heroicon-o-pencil-square' : 'heroicon-o-plus')
                    ->color(fn($record) => $record->kendala ? 'info' : 'warning')

                    ->schema([
                        Textarea::make('kendala')
                            ->label('Kendala')
                            ->required()
                            ->rows(4),
                    ])

                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'kendala' => $record->kendala ?? '',
                        ]);
                    })

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

                // Hilang jika sudah divalidasi
                EditAction::make()
                    ->visible(fn($record) => $record->validasiTerakhir?->status !== 'divalidasi'),

                DeleteAction::make()
                    ->visible(fn($record) => $record->validasiTerakhir?->status !== 'divalidasi'),

                // View boleh tetap tampil
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
