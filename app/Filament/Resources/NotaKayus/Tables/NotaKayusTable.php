<?php

namespace App\Filament\Resources\NotaKayus\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NotaKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_nota')
                    ->searchable(),
                TextColumn::make('info_kayu')
                    ->label('Info Kayu')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if (!$record->kayuMasuk)
                            return '-';

                        $seri = $record->kayuMasuk->seri ?? '-';
                        $namaSupplier = $record->kayuMasuk->penggunaanSupplier?->nama_supplier ?? '-';
                        $noTelepon = $record->kayuMasuk->penggunaanSupplier?->no_telepon ?? '-';

                        return "Seri {$seri} - {$namaSupplier} ({$noTelepon})";
                    }),

                TextColumn::make('penanggung_jawab')
                    ->searchable(),
                TextColumn::make('penerima')
                    ->searchable(),
                TextColumn::make('satpam')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge() // ubah jadi badge
                    ->colors([
                        'secondary' => 'Belum Diperiksa',
                        'success' => fn($state) => str_contains($state, 'Sudah Diperiksa'),
                        'warning' => fn($state) => str_contains($state, 'Menunggu'),
                        'danger' => fn($state) => str_contains($state, 'Ditolak'),
                    ]),
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
            ->recordActions([
                Action::make('cek')
                    ->label('Tandai Sudah Diperiksa')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'Belum Diperiksa') // tombol hanya muncul kalau belum diperiksa
                    ->action(function ($record) {
                        $user = Auth::user();
                        $record->status = "Sudah Diperiksa oleh {$user->name}";
                        $record->save();
                    })
                    ->requiresConfirmation() // opsional, kalau mau ada popup konfirmasi
                    ->successNotificationTitle('Status berhasil diperbarui'),
                Action::make('print')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('green')
                    ->url(fn($record) => route('nota-kayu.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status !== 'Belum Diperiksa') // tombol hanya muncul jika sudah diperiksa
                    ->disabled(
                        fn($record) =>
                        !$record->kayuMasuk?->detailTurusanKayus()->exists() // tetap pakai logika disable sebelumnya
                    ),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
