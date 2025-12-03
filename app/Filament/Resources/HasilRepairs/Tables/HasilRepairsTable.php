<?php

namespace App\Filament\Resources\HasilRepairs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

use App\Models\RencanaRepair;
use App\Models\HasilRepair;

class HasilRepairsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            // -----------------------------
            // QUERY UTAMA
            // -----------------------------
            ->query(function () {
                return RencanaRepair::query()
                    ->leftJoin('hasil_repairs', 'hasil_repairs.id_rencana_repair', '=', 'rencana_repairs.id')
                    ->select([
                        'rencana_repairs.*',
                        'hasil_repairs.jumlah as jumlah_hasil',
                        'hasil_repairs.id as hasil_id',
                    ])
                    ->with([
                        'ukuran',
                        'jenisKayu',
                        'rencanaPegawai.pegawai',
                    ]);
            })

            // -----------------------------
            // COLUMNS
            // -----------------------------
            ->columns([
                TextColumn::make('ukuran.dimensi')
                    ->label('Ukuran')
                    ->formatStateUsing(fn($state) => $state ? $state . ' × 0.5' : '-')
                    ->sortable(),

                TextColumn::make('jenisKayu.nama_kayu')
                    ->label('Jenis Kayu')
                    ->sortable(),

                TextColumn::make('kw')
                    ->label('KW')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('rencanaPegawai.nomor_meja')
                    ->label('Meja')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $state
                        ? "Meja {$state} - " . ($record->rencanaPegawai?->pegawai?->nama_pegawai ?? 'Tanpa Nama')
                        : '-'
                    )
                    ->badge()
                    ->color('info'),

                TextColumn::make('jumlah_hasil')
                    ->label('Hasil Produksi')
                    ->default(0)
                    ->numeric()
                    ->suffix(' lembar')
                    ->badge()
                    ->size('xl')
                    ->weight('bold')
                    ->color(fn($state) => $state >= 60 ? 'success' : ($state >= 40 ? 'warning' : 'danger')),
            ])

            // -----------------------------
            // RECORD ACTIONS
            // -----------------------------
            ->recordActions([

                // Tambah hasil
                Action::make('tambah')
                    ->label('Tambah')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->size('lg')
                    ->form([
                        TextInput::make('tambah')
                            ->label('Tambah Berapa Lembar?')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->default(1)
                            ->required()
                            ->prefix('+')
                            ->suffix(' lembar')
                            ->autofocus(),
                    ])
                    ->action(function ($record, array $data) {
                        $tambah = (int) $data['tambah'];

                        if (!$record->hasil_id) {
                            HasilRepair::create([
                                'id_rencana_repair' => $record->id,
                                'id_produksi_repair' => $record->id_produksi_repair,
                                'jumlah' => $tambah,
                            ]);
                        } else {
                            HasilRepair::where('id', $record->hasil_id)
                                ->increment('jumlah', $tambah);
                        }

                        Notification::make()
                            ->success()
                            ->title("Berhasil menambah $tambah lembar!")
                            ->send();
                    })
                    ->modalHeading(fn($record) => "Tambah Hasil - Meja " . ($record->rencanaPegawai?->nomor_meja ?? 'Unknown'))
                    ->modalSubmitActionLabel('Tambah Sekarang'),

                // Edit hasil
                Action::make('edit_hasil')
                    ->label('Edit Hasil')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->form([
                        TextInput::make('jumlah_hasil')
                            ->label('Jumlah Hasil Produksi')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(fn($record) => $record->jumlah_hasil ?? 0),
                    ])
                    ->action(function ($record, array $data) {
                        if ($record->hasil_id) {
                            HasilRepair::where('id', $record->hasil_id)
                                ->update(['jumlah' => $data['jumlah_hasil']]);
                        } else {
                            HasilRepair::create([
                                'id_rencana_repair' => $record->id,
                                'id_produksi_repair' => $record->id_produksi_repair,
                                'jumlah' => $data['jumlah_hasil'],
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Hasil berhasil diperbarui!')
                            ->send();
                    })
                    ->modalHeading(fn($record) => "Edit Hasil - Meja " . ($record->rencanaPegawai?->nomor_meja ?? 'Unknown'))
                    ->modalSubmitActionLabel('Simpan Perubahan'),

                // Delete hasil
                Action::make('delete_hasil')
                    ->label('Hapus Hasil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->hasil_id) {
                            HasilRepair::where('id', $record->hasil_id)->delete();

                            Notification::make()
                                ->success()
                                ->title('Hasil berhasil dihapus!')
                                ->send();
                        } else {
                            Notification::make()
                                ->warning()
                                ->title('Tidak ada data hasil untuk dihapus')
                                ->send();
                        }
                    }),
            ])

            // -----------------------------
            // BULK ACTIONS
            // -----------------------------
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $r) {
                                if ($r->hasil_id) {
                                    HasilRepair::where('id', $r->hasil_id)->delete();
                                }
                            }
                        }),
                ]),
            ])

            ->defaultSort('id', 'asc')
            ->poll('6s')

            ->emptyStateHeading('Belum ada rencana repair')
            ->emptyStateDescription('Isi rencana repair, maka hasil akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-inbox-stack');
    }
}
