<?php

namespace App\Filament\Resources\PlatformHasilHps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;

class PlatformHasilHpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                /*
                 * MESIN
                 */
                TextColumn::make('mesin.nama_mesin')
                    ->label('Mesin')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('no_palet')
                    ->label('No. Palet')
                    ->searchable(),

                /*
                 * JENIS BARANG (dari BarangSetengahJadiHp)
                 */
                TextColumn::make('barangSetengahJadiHp.jenisBarang.nama_jenis_barang')
                    ->label('Jenis Barang')
                    ->searchable()
                    ->placeholder('-'),

                /*
                 * GRADE
                 */
                TextColumn::make('barangSetengahJadiHp.grade.nama_grade')
                    ->label('Grade')
                    ->searchable()
                    ->placeholder('-'),

                /*
                 * UKURAN
                 */
                TextColumn::make('barangSetengahJadiHp.ukuran.nama_ukuran')
                    ->label('Ukuran')
                    ->searchable()
                    ->placeholder('-'),

                /*
                 * ISI
                 */
                TextColumn::make('isi')
                    ->label('Jumlah Lembar'),
            ])

            ->filters([])

            ->headerActions([
                CreateAction::make()
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])

            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),

                DeleteAction::make()
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->hidden(fn ($livewire) =>
                            $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                        ),
                ]),
            ]);
    }
}
