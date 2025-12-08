<?php

namespace App\Filament\Resources\DetailKayuMasuks\Tables;

use App\Models\DetailKayuMasuk;
use App\Models\Lahan;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Collection;

class DetailKayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()

            // recordClasses tetap aman karena hanya membaca property model
            ->recordClasses(function ($record) {
                $grade = (int) ($record->grade ?? 0);

                return match ($grade) {
                    1 => 'bg-opacity-5 filament-row-grade-a',
                    2 => 'bg-opacity-5 filament-row-grade-b',
                    default => null,
                };
            })

            ->columns([
                // Gunakan dot-notation relasi langsung sehingga Filament membuat JOIN yang benar.
                TextColumn::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Gabungkan state dari relasi + atribut model via formatStateUsing.
                TextColumn::make('jenisKayu.nama_kayu')
                    ->label('Kayu')
                    ->formatStateUsing(function ($state, $record) {
                        $namaKayu = $state ?? '-';
                        $panjang = $record->panjang ?? '-';

                        // Normalisasi grade (terima angka atau huruf)
                        $raw = trim((string) ($record->grade ?? ''));
                        $rawUpper = strtoupper($raw);
                        $gradeNorm = is_numeric($rawUpper) ? (int) $rawUpper : $rawUpper;

                        $grade = match ($gradeNorm) {
                            1, '1', 'A' => 'A',
                            2, '2', 'B' => 'B',
                            default => '-',
                        };

                        return "{$namaKayu} {$panjang} ({$grade})";
                    })
                    ->searchable()
                    // sortable() on relation column will generate proper join-sort
                    ->sortable(),

                TextColumn::make('diameter')
                    ->label('Diameter')
                    ->numeric()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_batang')
                    ->label('Batang')
                    ->numeric()
                    ->suffix(' btg')
                    ->searchable()
                    ->sortable(),

                // Model sudah memiliki accessor `getKubikasiAttribute` sehingga kita bisa gunakan kolom 'kubikasi'.
                // Jangan set sortable() karena kubikasi adalah accessor (bukan kolom DB).
                TextColumn::make('kubikasi')
                    ->label('Kubikasi')
                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format($state, 6, ',', '.'))
                    ->suffix(' m³')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignRight(),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('updatedBy.name')
                    ->label('Diubah Oleh')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // Grouping berdasarkan relasi dot-notation juga aman
            ->groups([
                Group::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->collapsible()
                    ->orderQueryUsing(function ($query, $direction) {
                        // join eksplisit agar Filament dapat mengurutkan grup berdasarkan kolom relasi
                        return $query
                            ->join('lahans', 'detail_kayu_masuks.id_lahan', '=', 'lahans.id')
                            ->orderBy('lahans.kode_lahan', $direction)
                            ->select('detail_kayu_masuks.*');
                    })
                    ->getTitleFromRecordUsing(function ($record, $records = null) {
                        $kode = $record->lahan?->kode_lahan ?? '-';
                        $nama = $record->lahan?->nama_lahan ?? '-';
                        $jenis_kayu = $record->jenisKayu?->nama_kayu ?? '-';

                        $parentId = $record->id_kayu_masuk ?? $record->kayu_masuk_id ?? null;
                        $lahanId = $record->id_lahan;

                        if ($records instanceof Collection && $records->isNotEmpty()) {
                            $filtered = $records
                                ->where('id_kayu_masuk', $parentId)
                                ->where('id_lahan', $lahanId);

                            $totalBatang = $filtered->sum(fn($r) => (int) ($r->jumlah_batang ?? 0));

                            $totalKubikasi = $filtered->sum(function ($r) {
                                $diameter = $r->diameter ?? 0;
                                $jumlah = $r->jumlah_batang ?? 0;
                                $panjang = $r->panjang ?? 0;
                                return ($panjang * $diameter * $diameter * $jumlah * 0.785) / 1000000;
                            });

                        } else {
                            $query = DetailKayuMasuk::query()
                                ->where('id_kayu_masuk', $parentId)
                                ->where('id_lahan', $lahanId)
                                ->get();

                            $totalBatang = $query->sum('jumlah_batang');

                            $totalKubikasi = $query->sum(function ($r) {
                                $diameter = $r->diameter ?? 0;
                                $jumlah = $r->jumlah_batang ?? 0;
                                $panjang = $r->panjang ?? 0;
                                return ($panjang * $diameter * $diameter * $jumlah * 0.785) / 1000000;
                            });
                        }

                        $kubikasiFormatted = number_format($totalKubikasi, 4, ',', '.');

                        return "{$kode} {$nama} {$jenis_kayu} - {$totalBatang} batang ({$kubikasiFormatted} m³)";
                    }),
            ])

            ->defaultGroup('lahan.kode_lahan')
            ->groupingSettingsHidden()
            ->filters([
                // tambahkan filter yang diperlukan di sini
            ])
            ->defaultSort('created_at', 'desc')

            ->headerActions([
                CreateAction::make(),
            ])

            ->recordActions([
                Action::make('kurangiBatang')
                    ->label('')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->button()
                    ->outlined(false)
                    ->size('sm')
                    ->action(function (DetailKayuMasuk $record) {
                        if ($record->jumlah_batang > 0) {
                            $record->decrement('jumlah_batang');
                        }
                    }),

                Action::make('tambahBatang')
                    ->label('')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->button()
                    ->outlined(false)
                    ->size('sm')
                    ->action(function (DetailKayuMasuk $record) {
                        $record->increment('jumlah_batang');
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('update_lahan')
                        ->label('Update Lahan')
                        ->icon('heroicon-o-map')
                        ->schema([
                            Select::make('id_lahan')
                                ->label('Lahan Baru')
                                ->options(Lahan::pluck('kode_lahan', 'id')->toArray())
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'id_lahan' => $data['id_lahan'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(fn($count) => "{$count} data berhasil diupdate"),

                    BulkAction::make('update_panjang')
                        ->label('Update Panjang')
                        ->icon('heroicon-o-arrows-up-down')
                        ->schema([
                            Select::make('panjang')
                                ->label('Panjang Baru')
                                ->options([
                                    130 => '130',
                                    260 => '260',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'panjang' => $data['panjang'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(fn($count) => "{$count} data berhasil diupdate"),
                ]),
            ]);
    }
}
