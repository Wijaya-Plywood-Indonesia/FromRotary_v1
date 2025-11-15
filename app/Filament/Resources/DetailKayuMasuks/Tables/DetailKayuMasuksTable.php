<?php

namespace App\Filament\Resources\DetailKayuMasuks\Tables;

use App\Models\DetailKayuMasuk;
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
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Collection;

class DetailKayuMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table


            ->striped() // gunakan striping bawaan
            ->recordClasses(function ($record) {
                // Tambahkan class kondisional per record (grade)
                // Kita mengembalikan string atau array kelas tailwind yang valid.
                $grade = (int) ($record->grade ?? 0);

                return match ($grade) {
                    1 => 'bg-opacity-5 filament-row-grade-a', // tambahkan hook class custom
                    2 => 'bg-opacity-5 filament-row-grade-b',
                    default => null,
                };
            })
            ->columns([

                // TextColumn::make('no')
                //     ->label('No')
                //     ->rowIndex()
                //     ->alignCenter()
                //     ->width('60px'),

                TextColumn::make('lahan_display')
                    ->label('Lahan')
                    ->getStateUsing(fn($record) => "{$record->lahan->kode_lahan}")
                    ->sortable(['lahan.kode_lahan'])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(['lahan.kode_lahan']),

                TextColumn::make('keterangan_kayu')
                    ->label('Kayu')
                    ->getStateUsing(function ($record) {
                        $namaKayu = $record->jenisKayu?->nama_kayu ?? '-';
                        $panjang = $record->panjang ?? '-';
                        $grade = match ($record->grade) {
                            1 => 'A',
                            2 => 'B',
                            default => '-',
                        };

                        return "{$namaKayu} {$panjang} ({$grade})";
                    })
                    ->sortable(['jenisKayu.nama_kayu', 'panjang', 'grade']) // tetap bisa diurutkan
                    ->searchable(['jenisKayu.nama_kayu', 'panjang']) // bisa dicari juga
                    //  ->badge()
                    ->color(fn($record) => match ($record->grade) {
                        1 => 'success',
                        2 => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('diameter')
                    ->label('D')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('jumlah_batang')
                    ->label('Batang')
                    ->numeric()
                    ->suffix(' btg')
                    ->sortable(),
                TextColumn::make('kubikasi')
                    ->label('Kubikasi')
                    ->getStateUsing(function ($record) {
                        $diameter = $record->diameter ?? 0;
                        $jumlahBatang = $record->jumlah_batang ?? 0;

                        // Rumus: diameter × jumlah_batang × 0.785 / 1_000_000
                        $kubikasi = $diameter * $jumlahBatang * 0.785 / 1_000_000;

                        // Tampilkan hingga 6 angka di belakang koma
                        return number_format($kubikasi, 6, ',', '.');
                    })
                    ->suffix(' m³')
                    ->sortable()
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

            ])
            ->groups([
                Group::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->collapsible()
                    ->orderQueryUsing(function ($query, $direction) {
                        // supaya Filament tahu cara urutkan data saat ASC/DESC di UI
                        return $query
                            ->join('lahans', 'detail_kayu_masuks.id_lahan', '=', 'lahans.id')
                            ->orderBy('lahans.kode_lahan', $direction)
                            ->select('detail_kayu_masuks.*'); // penting untuk mencegah ambiguitas
                    })
                    ->getTitleFromRecordUsing(function ($record, $records = null) {
                        $kode = $record->lahan?->kode_lahan ?? '-';
                        $nama = $record->lahan?->nama_lahan ?? '-';
                        $jenis_kayu = $record->jenisKayu?->nama_kayu ?? '-';

                        if ($records instanceof Collection && $records->isNotEmpty()) {
                            $totalBatang = $records->count();
                            $totalKubikasi = $records->sum(fn($r) => (float) $r->kubikasi);
                        } else {
                            $query = DetailKayuMasuk::query()
                                ->where('id_lahan', $record->id_lahan)
                                ->get();
                            $totalBatang = $query->count();
                            $totalKubikasi = $query->sum(fn($r) => (float) $r->kubikasi);
                        }

                        $kubikasiFormatted = number_format($totalKubikasi, 4, ',', '.');
                        return "{$kode} {$nama} {$jenis_kayu} - {$totalBatang} batang ({$kubikasiFormatted} m³)";
                    }),
            ])
            ->defaultGroup('lahan.kode_lahan')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('total_kubikasi')
                    ->label(function () {
                        // Ambil semua data DetailKayuMasuk
                        $totalKubikasi = DetailKayuMasuk::all()
                            ->sum(
                                fn($item) =>
                                ($item->diameter ?? 0) * ($item->jumlah_batang ?? 0) * 0.785 / 1_000_000
                            );

                        return 'Total Kubikasi = ' . number_format($totalKubikasi, 6, ',', '.') . ' m³';
                    })
                    ->disabled() // Tidak bisa diklik
                    ->color('gray')
                    ->button() // Supaya tampil seperti label di header
                    ->outlined()
                    ->icon('heroicon-o-cube'),

                Action::make('sinkron_kubikasi')
                    ->label('Sinkron Total Kubikasi')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire) {
                        $record = $livewire->ownerRecord; // model KayuMasuk (parent)
            
                        // Pastikan kita mendapatkan collection (bukan null)
                        $details = $record->detailMasukanKayu()->get();

                        $totalKubikasi = $details->sum(function ($item) {
                            return ($item->diameter ?? 0) * ($item->jumlah_batang ?? 0) * 0.785 / 1000000;
                        });

                        // Pastikan tidak null (cast ke float)
                        $totalKubikasi = (float) $totalKubikasi;

                        $record->update(['kubikasi' => $totalKubikasi]);

                        Notification::make()
                            ->title('Total kubikasi berhasil disinkronkan!')
                            ->body('Total: ' . number_format($totalKubikasi, 6, ',', '.') . ' m³')
                            ->success()
                            ->send();

                        // 🔄 Refresh halaman setelah update
                        // $livewire->dispatchBrowserEvent('reload');
            
                    })
                    ->after(function ($livewire) {
                        // Setelah aksi selesai, reload komponen saat ini (bukan full page)
                        $livewire->dispatch('$refresh');

                        // Kalau mau full reload (halaman benar-benar segar):
                        $livewire->js('window.location.reload()');
                    }),

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
                            $record->jumlah_batang = $record->jumlah_batang - 1;
                            $record->save();
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
                        $record->jumlah_batang = $record->jumlah_batang + 1;
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
