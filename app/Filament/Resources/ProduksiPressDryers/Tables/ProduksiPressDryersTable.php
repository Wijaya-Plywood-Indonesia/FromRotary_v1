<?php

namespace App\Filament\Resources\ProduksiPressDryers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;

class ProduksiPressDryersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_produksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('shift')
                    ->label('Shift')
                    ->searchable(),

            ])
            ->filters([
                Filter::make('tanggal_produksi')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when(
                                $data['from'],
                                fn($query, $date) => $query->whereDate('tanggal_produksi', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn($query, $date) => $query->whereDate('tanggal_produksi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
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
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
