<?php

namespace App\Filament\Resources\ProduksiStiks\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;

class ProduksiStiksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_produksi')
                    ->label('Tanggal Produksi')
                    ->date('d/m/Y') // Format lebih manusiawi
                    ->sortable()
                    ->searchable(),

                // ✅ Perbaikan Badge Status: Menggunakan TextColumn + badge()
                TextColumn::make('validasiTerakhir.status')
                    ->label('Status Validasi')
                    ->badge() // Menggantikan BadgeColumn
                    ->color(fn (string $state): string => match ($state) {
                        'divalidasi' => 'success',
                        'ditangguhkan' => 'warning',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'divalidasi' => 'heroicon-m-check-circle',
                        'ditolak' => 'heroicon-m-x-circle',
                        'ditangguhkan' => 'heroicon-m-exclamation-circle',
                        default => 'heroicon-m-clock',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kendala')
                    ->label('Kendala')
                    ->limit(30)
                    ->placeholder('Tidak ada kendala')
                    ->tooltip(fn($record): ?string => $record->kendala)
                    ->toggleable(isToggledHiddenByDefault: false),

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
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('tanggal_produksi', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('tanggal_produksi', '<=', $date));
                    }),
            ])
            ->recordActions([
                // ✅ Kelola Kendala
                Action::make('kelola_kendala')
                    ->label(fn($record) => $record->kendala ? 'Edit Kendala' : 'Tambah Kendala')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color(fn($record) => $record->kendala ? 'info' : 'gray')
                    // Sembunyikan jika sudah divalidasi
                    ->visible(fn($record) => $record->validasiTerakhir?->status !== 'divalidasi')
                    ->form([
                        Textarea::make('kendala')
                            ->label('Catatan Kendala Lapangan')
                            ->required()
                            ->rows(4),
                    ])
                    ->mountUsing(fn($form, $record) => $form->fill(['kendala' => $record->kendala]))
                    ->action(function (array $data, $record): void {
                        $record->update(['kendala' => trim($data['kendala'])]);
                        Notification::make()->title('Kendala berhasil disimpan')->success()->send();
                    })
                    ->modalHeading('Manajemen Kendala')
                    ->modalWidth('lg'),

                EditAction::make()
                    ->visible(fn($record) => $record->validasiTerakhir?->status !== 'divalidasi'),

                DeleteAction::make()
                    ->visible(fn($record) => $record->validasiTerakhir?->status !== 'divalidasi'),

                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih (Hanya yang belum divalidasi)')
                        ->action(function (DeleteBulkAction $action) {
                            $action->getRecords()->each(function ($record) {
                                if ($record->validasiTerakhir?->status !== 'divalidasi') {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ]);
    }
}