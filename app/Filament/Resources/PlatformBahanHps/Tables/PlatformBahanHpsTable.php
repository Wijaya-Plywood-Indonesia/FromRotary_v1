<?php

namespace App\Filament\Resources\PlatformBahanHps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;

class PlatformBahanHpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                // KOLOM BARU: Lapisan ke- (Memerlukan relasi detailKomposisi)
                TextColumn::make('detailKomposisi.lapisan')
                    ->label('Lapisan ke-')
                    ->sortable()
                    ->placeholder('N/A'),
                    
                // Kolom untuk Jenis Barang/Kayu (Melalui BarangSetengahJadiHp)
                TextColumn::make('barangSetengahJadiHp.jenisBarang.nama_jenis_barang')
                    ->label('Jenis Barang/Kayu')
                    ->searchable()
                    ->placeholder('N/A'),

                // Kolom untuk Ukuran (Melalui BarangSetengahJadiHp)
                TextColumn::make('barangSetengahJadiHp.ukuran.nama_ukuran')
                    ->label('Ukuran')
                    ->searchable(false)
                    ->placeholder('Ukuran'),

                // Kolom untuk Kualitas/Grade (Melalui BarangSetengahJadiHp)
                TextColumn::make('barangSetengahJadiHp.grade.nama_grade')
                    ->label('Kualitas (Grade)')
                    ->searchable()
                    ->placeholder('N/A'),

                // Kolom untuk Jumlah Lembar (Langsung dari PlatformBahanHp)
                TextColumn::make('isi')
                    ->label('Jumlah Lembar'),

                // Note: Kolom 'no_palet' telah dipindahkan ke awal, tetapi jika Anda menggunakannya, 
                // pastikan relasi KW (kualitas lama) dan Jenis Kayu (lama) diganti.
                // TextColumn::make('no_palet') telah dihapus dari sini karena fokus ke Lapisan/Tebal.
                // Jika masih butuh No. Palet, pindahkan ke atas.
                // Jika kolom 'kw' masih dibutuhkan, gunakan relasi baru jika data ada di BarangSetengahJadiHp.
            ])
            ->filters([
                // Tempat filter jika Anda membutuhkannya
            ])
            ->headerActions([
                // Create Action — HILANG jika status sudah divalidasi
                CreateAction::make()
                    ->hidden(
                        fn($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])
            ->recordActions([
                // Edit Action — HILANG jika status sudah divalidasi
                EditAction::make()
                    ->hidden(
                        fn($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),

                // Delete Action — HILANG jika status sudah divalidasi
                DeleteAction::make()
                    ->hidden(
                        fn($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->hidden(
                            fn($livewire) =>
                            $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                        ),
                ]),
            ]);
    }
}