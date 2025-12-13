<?php

namespace App\Filament\Resources\VeneerBahanHps\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class VeneerBahanHpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                /*
                |------------------------------------------------------------
                | LAPISAN
                |------------------------------------------------------------
                */
                TextColumn::make('detailKomposisi.lapisan')
                    ->label('Lapisan')
                    ->sortable()
                    ->alignCenter(),

                /*
                |------------------------------------------------------------
                | JENIS BARANG (KETERANGAN + JENIS KAYU)
                |------------------------------------------------------------
                */
                TextColumn::make('jenis_bahan')
                    ->label('Jenis Barang')
                    ->getStateUsing(fn ($record) =>
                        trim(
                            ($record->detailKomposisi?->keterangan ?? '-') .
                            ' | ' .
                            ($record->barangSetengahJadiHp?->jenisBarang?->nama_jenis_barang ?? '-')
                        )
                    )
                    ->wrap()
                    ->searchable(),

                /*
                |------------------------------------------------------------
                | UKURAN
                |------------------------------------------------------------
                */
                TextColumn::make('barangSetengahJadiHp.ukuran.nama_ukuran')
                    ->label('Ukuran')
                    ->wrap(),

                /*
                |------------------------------------------------------------
                | GRADE
                |------------------------------------------------------------
                */
                BadgeColumn::make('barangSetengahJadiHp.grade.nama_grade')
                    ->label('Grade')
                    ->alignCenter(),

                /*
                |------------------------------------------------------------
                | JUMLAH LEMBAR
                |------------------------------------------------------------
                */
                TextColumn::make('isi')
                    ->label('Jumlah Lembar')
                    ->numeric()
                    ->alignCenter(),
            ])

            /*
            |------------------------------------------------------------
            | HEADER ACTION
            |------------------------------------------------------------
            */
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Veneer')
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])

            /*
            |------------------------------------------------------------
            | RECORD ACTION
            |------------------------------------------------------------
            */
            ->actions([
                EditAction::make()
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),

                DeleteAction::make()
                    ->hidden(fn ($livewire) =>
                        $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                    ),
            ])

            /*
            |------------------------------------------------------------
            | BULK ACTION
            |------------------------------------------------------------
            */
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->hidden(fn ($livewire) =>
                            $livewire->ownerRecord?->validasiTerakhir?->status === 'divalidasi'
                        ),
                ]),
            ])

            ->defaultSort('detailKomposisi.lapisan', 'asc');
    }
}
