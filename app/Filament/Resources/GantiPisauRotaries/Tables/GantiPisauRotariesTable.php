<?php

namespace App\Filament\Resources\GantiPisauRotaries\Tables;

use App\Models\GantiPisauRotary;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GantiPisauRotariesTable
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('buat_otomatis')
                ->label('Ganti Pisau !')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->requiresConfirmation() // kalau ingin konfirmasi
                ->action(function () {
                    GantiPisauRotary::create([
                        'jam_mulai_ganti_pisau' => now()->format('H:i'),
                        'jam_selesai_ganti' => now()->format('H:i'),
                        // tambahkan field lain jika perlu nilai default juga
                    ]);
                })
                ->successNotificationTitle('Pisau Sedang Diganti.!')
        ];
    }
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('jam_mulai_ganti_pisau')
                    ->time()
                    ->sortable(),
                TextColumn::make('jam_selesai_ganti')
                    ->time()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(), // 👈 ini yang munculkan tombol "Tambah"
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('selesai_ganti')
                    ->label('Pisau Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        $record->jam_selesai_ganti <= $record->jam_mulai_ganti
                    )
                    ->requiresConfirmation()
                    ->action(function ($record, RelationManager $livewire) {
                        $record->update([
                            'jam_selesai_ganti' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
                        ]);

                        Notification::make()
                            ->title('Pisau selesai diganti!')
                            ->success()
                            ->send();

                        $livewire->refreshTable();
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
