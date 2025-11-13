<?php

namespace App\Filament\Resources\NotaKayus\Tables;

use App\Models\DetailKayuMasuk;
use App\Models\DetailTurusanKayu;
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
                    ->label('PJ')
                    ->searchable(),
                TextColumn::make('total_summary')
                    ->label('Rekap Turusan')
                    ->getStateUsing(function ($record) {
                        if (!$record->kayuMasuk) {
                            return "0 Batang\n0.0000 m³";
                        }

                        $total = DetailTurusanKayu::hitungTotalByKayuMasuk($record->kayuMasuk->id);
                        $batang = number_format($total['total_batang']);
                        $kubikasi = number_format($total['total_kubikasi'], 4);

                        return "{$batang} Batang\n{$kubikasi} m³";
                    })
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn(string $state) => str_replace("\n", '<br>', e($state)))
                    ->html() // penting agar <br> terbaca sebagai baris baru
                    ->alignCenter(),
                TextColumn::make('total_summary2')
                    ->label('Rekap Turusan 2')
                    ->getStateUsing(function ($record) {
                        if (!$record->kayuMasuk) {
                            return "0 Batang\n0.0000 m³";
                        }

                        $total = DetailKayuMasuk::hitungTotalByKayuMasuk($record->kayuMasuk->id);
                        $batang = number_format($total['total_batang']);
                        $kubikasi = number_format($total['total_kubikasi'], 4);

                        return "{$batang} Batang\n{$kubikasi} m³";
                    })
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn(string $state) => str_replace("\n", '<br>', e($state)))
                    ->html() // penting agar <br> terbaca sebagai baris baru
                    ->alignCenter(),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('cek')
                    ->label('Tandai Sudah Diperiksa')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        // Pastikan status masih "Belum Diperiksa"
                        if ($record->status !== 'Belum Diperiksa') {
                            return false;
                        }

                        // Pastikan ada relasi kayuMasuk
                        if (!$record->kayuMasuk) {
                            return false;
                        }

                        // Ambil total dari kedua sumber
                        $total1 = DetailTurusanKayu::hitungTotalByKayuMasuk($record->kayuMasuk->id);
                        $total2 = DetailKayuMasuk::hitungTotalByKayuMasuk($record->kayuMasuk->id);

                        // Bandingkan total batang dan kubikasi
                        $batangSama = $total1['total_batang'] == $total2['total_batang'];
                        $kubikasiSama = abs($total1['total_kubikasi'] - $total2['total_kubikasi']) < 0.0001; // toleransi desimal
            
                        return $batangSama && $kubikasiSama;
                    })
                    ->action(function ($record) {
                        $user = Auth::user();
                        $record->status = "Sudah Diperiksa oleh {$user->name}";
                        $record->save();
                    })
                    ->requiresConfirmation()
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
