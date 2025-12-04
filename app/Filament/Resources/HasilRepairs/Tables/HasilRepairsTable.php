<?php

namespace App\Filament\Resources\HasilRepairs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\RencanaRepair;
use App\Models\HasilRepair;

class HasilRepairsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            // ============================================
            // QUERY UTAMA - FIXED SUBQUERY ALIASES
            // ============================================
            ->query(function () {
                return RencanaRepair::query()
                    ->select([
                        'rencana_repairs.id_modal_repair',
                        'rencana_repairs.kw',
                        'rencana_repairs.id_produksi_repair',
                        'rencana_pegawais.nomor_meja',
                        DB::raw('MIN(rencana_repairs.id) as id'),
                        DB::raw('GROUP_CONCAT(DISTINCT rencana_repairs.id ORDER BY rencana_repairs.id SEPARATOR ",") as rencana_ids'),
                        DB::raw('COUNT(DISTINCT rencana_repairs.id) as jumlah_pekerja'),
                        DB::raw('SUM(COALESCE(hasil_repairs.jumlah, 0)) as total_hasil'),
                    ])
                    ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                    ->leftJoin('hasil_repairs', 'hasil_repairs.id_rencana_repair', '=', 'rencana_repairs.id')
                    ->groupBy([
                        'rencana_repairs.id_modal_repair',
                        'rencana_repairs.kw',
                        'rencana_repairs.id_produksi_repair',
                        'rencana_pegawais.nomor_meja',
                    ])
                    ->orderBy('rencana_pegawais.nomor_meja', 'asc');
            })

            // ============================================
            // COLUMNS
            // ============================================
            ->columns([
                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->state(function ($record) {
                        $rencana = RencanaRepair::with('modalRepairs.ukuran')->find($record->id);
                        return $rencana?->modalRepairs?->ukuran?->dimensi ?? '-';
                    })
                    ->formatStateUsing(fn($state) => $state)
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('jenis_kayu')
                    ->label('Jenis Kayu')
                    ->state(function ($record) {
                        $rencana = RencanaRepair::with('modalRepairs.jenisKayu')->find($record->id);
                        return $rencana?->modalRepairs?->jenisKayu?->nama_kayu ?? '-';
                    })
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('kw')
                    ->label('KW')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nomor_meja')
                    ->label('Meja')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        "Meja {$state} - {$record->jumlah_pekerja} pekerja"
                    )
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total_hasil')
                    ->label('Hasil Produksi')
                    ->default(0)
                    ->numeric()
                    ->suffix(' lembar')
                    ->badge()
                    ->size('xl')
                    ->weight('bold')
                    ->color(fn($state) => $state >= 60 ? 'success' : ($state >= 40 ? 'warning' : 'danger')),
            ])
            // ============================================
            // ACTIONS - CINEMA MODE 🎬
            // ============================================
            ->recordActions([
                // ➕ TAMBAH HASIL
                Action::make('tambah')
                    ->label('Tambah')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('tambah')
                            ->label('Tambah Berapa Lembar? (Per Pekerja)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->default(1)
                            ->required()
                            ->prefix('+')
                            ->suffix(' lembar')
                            ->helperText(fn($record) => "Akan ditambahkan untuk {$record->jumlah_pekerja} pekerja di Meja {$record->nomor_meja}")
                            ->autofocus(),
                    ])
                    ->action(function ($record, array $data) {
                        $tambah = (int) $data['tambah'];

                        // Get IDs dengan fallback
                        $rencanaIds = array_filter(
                            array_map('intval', explode(',', $record->rencana_ids ?? ''))
                        );

                        if (empty($rencanaIds)) {
                            $rencanaIds = RencanaRepair::query()
                                ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                                ->where('rencana_repairs.id_modal_repair', $record->id_modal_repair)
                                ->where('rencana_repairs.kw', $record->kw)
                                ->where('rencana_repairs.id_produksi_repair', $record->id_produksi_repair)
                                ->where('rencana_pegawais.nomor_meja', $record->nomor_meja)
                                ->pluck('rencana_repairs.id')
                                ->toArray();
                        }

                        foreach ($rencanaIds as $rencanaId) {
                            $rencana = RencanaRepair::find($rencanaId);
                            if (!$rencana)
                                continue;

                            $hasilExist = HasilRepair::where('id_rencana_repair', $rencanaId)->first();

                            if (!$hasilExist) {
                                HasilRepair::create([
                                    'id_rencana_repair' => $rencanaId,
                                    'id_produksi_repair' => $rencana->id_produksi_repair,
                                    'jumlah' => $tambah,
                                ]);
                            } else {
                                $hasilExist->increment('jumlah', $tambah);
                            }
                        }

                        $totalAdded = $tambah * count($rencanaIds);

                        Notification::make()
                            ->success()
                            ->title("✅ Berhasil menambah {$tambah} lembar per pekerja!")
                            ->body("🎯 Total: {$totalAdded} lembar untuk " . count($rencanaIds) . " pekerja di Meja {$record->nomor_meja}")
                            ->send();
                    })
                    ->modalHeading(fn($record) => "➕ Tambah Hasil - Meja {$record->nomor_meja}")
                    ->modalSubmitActionLabel('🚀 Tambah Sekarang'),

                Action::make('view_pekerja')
                    ->label('Lihat Pekerja')
                    ->icon('heroicon-o-users')
                    ->color('gray')
                    ->modalContent(function ($record) {
                        // Parse IDs dengan aman
                        $rencanaIdsString = $record->rencana_ids ?? '';
                        $rencanaIds = array_filter(
                            array_map('intval', explode(',', $rencanaIdsString))
                        );

                        // FALLBACK: Query langsung jika GROUP_CONCAT bermasalah
                        if (empty($rencanaIds)) {
                            Log::warning('GROUP_CONCAT returned empty, using fallback query');

                            $rencanaIds = RencanaRepair::query()
                                ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                                ->where('rencana_repairs.id_modal_repair', $record->id_modal_repair)
                                ->where('rencana_repairs.kw', $record->kw)
                                ->where('rencana_repairs.id_produksi_repair', $record->id_produksi_repair)
                                ->where('rencana_pegawais.nomor_meja', $record->nomor_meja)
                                ->pluck('rencana_repairs.id')
                                ->toArray();
                        }

                        // Log untuk debug
                        Log::info('=== 🎬 VIEW PEKERJA CLICKED ===');
                        Log::info('Record ID: ' . $record->id);
                        Log::info('Nomor Meja: ' . $record->nomor_meja);
                        Log::info('Modal Repair ID: ' . $record->id_modal_repair);
                        Log::info('KW: ' . $record->kw);
                        Log::info('Rencana IDs String: ' . $rencanaIdsString);
                        Log::info('Rencana IDs Array: ', $rencanaIds);

                        // Query detail pekerja
                        $details = RencanaRepair::whereIn('id', $rencanaIds)
                            ->with([
                                'rencanaPegawai.pegawai',
                                'hasilRepairs',
                            ])
                            ->orderBy('id', 'asc')
                            ->get();

                        Log::info('✅ Details Count: ' . $details->count());
                        Log::info('Details IDs: ', $details->pluck('id')->toArray());

                        if ($details->count() !== count($rencanaIds)) {
                            Log::error('⚠️ MISMATCH! Expected ' . count($rencanaIds) . ' but got ' . $details->count());
                        }

                        return view('filament.tables.hasil-repair-detail', [
                            'meja' => $record->nomor_meja,
                            'details' => $details,
                        ]);
                    })
                    ->modalHeading(fn($record) => "🎬 Detail Pekerja - Meja {$record->nomor_meja}")
                    ->modalWidth('md')
                    ->slideOver()
                    ->modalFooterActions([]),


                // ✏️ EDIT HASIL
                Action::make('edit_hasil')
                    ->label('Edit Hasil')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->form([
                        TextInput::make('jumlah_per_pekerja')
                            ->label('Hasil Per Pekerja')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText(fn($record) => "Akan disimpan untuk {$record->jumlah_pekerja} pekerja")
                            ->default(
                                fn($record) =>
                                $record->jumlah_pekerja > 0
                                ? (int) ($record->total_hasil / $record->jumlah_pekerja)
                                : 0
                            )
                            ->suffix(' lembar'),
                    ])
                    ->action(function ($record, array $data) {
                        $jumlahPerPekerja = (int) $data['jumlah_per_pekerja'];

                        $rencanaIds = array_filter(
                            array_map('intval', explode(',', $record->rencana_ids ?? ''))
                        );

                        if (empty($rencanaIds)) {
                            $rencanaIds = RencanaRepair::query()
                                ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                                ->where('rencana_repairs.id_modal_repair', $record->id_modal_repair)
                                ->where('rencana_repairs.kw', $record->kw)
                                ->where('rencana_repairs.id_produksi_repair', $record->id_produksi_repair)
                                ->where('rencana_pegawais.nomor_meja', $record->nomor_meja)
                                ->pluck('rencana_repairs.id')
                                ->toArray();
                        }

                        foreach ($rencanaIds as $rencanaId) {
                            $rencana = RencanaRepair::find($rencanaId);
                            if (!$rencana)
                                continue;

                            $hasilExist = HasilRepair::where('id_rencana_repair', $rencanaId)->first();

                            if ($hasilExist) {
                                $hasilExist->update(['jumlah' => $jumlahPerPekerja]);
                            } else {
                                HasilRepair::create([
                                    'id_rencana_repair' => $rencanaId,
                                    'id_produksi_repair' => $rencana->id_produksi_repair,
                                    'jumlah' => $jumlahPerPekerja,
                                ]);
                            }
                        }

                        $totalSaved = $jumlahPerPekerja * count($rencanaIds);

                        Notification::make()
                            ->success()
                            ->title('✅ Hasil berhasil diperbarui!')
                            ->body("📊 Total: {$totalSaved} lembar untuk Meja {$record->nomor_meja}")
                            ->send();
                    })
                    ->modalHeading(fn($record) => "✏️ Edit Hasil - Meja {$record->nomor_meja}")
                    ->modalSubmitActionLabel('💾 Simpan Perubahan'),

                // 🗑️ DELETE HASIL
                Action::make('delete_hasil')
                    ->label('Hapus Hasil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(fn($record) => "⚠️ Akan menghapus hasil untuk {$record->jumlah_pekerja} pekerja di Meja {$record->nomor_meja}")
                    ->action(function ($record) {
                        $rencanaIds = array_filter(
                            array_map('intval', explode(',', $record->rencana_ids ?? ''))
                        );

                        if (empty($rencanaIds)) {
                            $rencanaIds = RencanaRepair::query()
                                ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                                ->where('rencana_repairs.id_modal_repair', $record->id_modal_repair)
                                ->where('rencana_repairs.kw', $record->kw)
                                ->where('rencana_repairs.id_produksi_repair', $record->id_produksi_repair)
                                ->where('rencana_pegawais.nomor_meja', $record->nomor_meja)
                                ->pluck('rencana_repairs.id')
                                ->toArray();
                        }

                        $deleted = HasilRepair::whereIn('id_rencana_repair', $rencanaIds)->delete();

                        if ($deleted > 0) {
                            Notification::make()
                                ->success()
                                ->title('✅ Hasil berhasil dihapus!')
                                ->body("🗑️ Dihapus untuk {$deleted} pekerja di Meja {$record->nomor_meja}")
                                ->send();
                        } else {
                            Notification::make()
                                ->warning()
                                ->title('⚠️ Tidak ada data hasil untuk dihapus')
                                ->send();
                        }
                    }),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $rencanaIds = array_filter(
                                    array_map('intval', explode(',', $record->rencana_ids ?? ''))
                                );

                                if (empty($rencanaIds)) {
                                    $rencanaIds = RencanaRepair::query()
                                        ->join('rencana_pegawais', 'rencana_pegawais.id', '=', 'rencana_repairs.id_rencana_pegawai')
                                        ->where('rencana_repairs.id_modal_repair', $record->id_modal_repair)
                                        ->where('rencana_repairs.kw', $record->kw)
                                        ->where('rencana_repairs.id_produksi_repair', $record->id_produksi_repair)
                                        ->where('rencana_pegawais.nomor_meja', $record->nomor_meja)
                                        ->pluck('rencana_repairs.id')
                                        ->toArray();
                                }

                                HasilRepair::whereIn('id_rencana_repair', $rencanaIds)->delete();
                            }
                        }),
                ]),
            ])

            ->defaultSort('nomor_meja', 'asc')
            ->poll('6s')

            ->emptyStateHeading('🎬 Belum ada rencana repair')
            ->emptyStateDescription('Tambahkan rencana repair untuk memulai produksi!')
            ->emptyStateIcon('heroicon-o-film');
    }
}