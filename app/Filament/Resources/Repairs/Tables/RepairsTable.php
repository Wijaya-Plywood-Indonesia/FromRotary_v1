<?php

namespace App\Filament\Resources\Repairs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RepairsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([


                TextColumn::make('ukuran.full_ukuran')
                    ->label('Ukuran Kayu')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->ukuran
                        ? "{$record->ukuran->panjang} × {$record->ukuran->lebar} × {$record->ukuran->tebal} cm"
                        : '—'
                    )
                    ->searchable(['ukurans.panjang', 'ukurans.lebar', 'ukurans.tebal'])
                    ->sortable()
                    ->wrap(),

                TextColumn::make('jenisKayu.kode_kayu')
                    ->label('Jenis Kayu')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kualitas')
                    ->searchable(),
                TextColumn::make('total_lembar')
                    ->label('Lembar')
                    ->numeric(locale: 'id')
                    ->sortable(),
                TextColumn::make('jam_kerja')
                    ->time()
                    ->sortable(),
                TextColumn::make('target')
                    ->label('Target')
                    ->numeric(locale: 'id')
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
                SelectFilter::make('kualitas')
                    ->options([
                        'KW1' => 'KW1',
                        'KW2' => 'KW2',
                        'KW3' => 'KW3',
                        'KW4' => 'KW4',
                    ]),
                SelectFilter::make('jenis_kayu')
                    ->relationship('jenisKayu', 'kode_kayu')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
