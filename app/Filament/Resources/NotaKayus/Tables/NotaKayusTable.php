<?php

namespace App\Filament\Resources\NotaKayus\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotaKayusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_kayu_masuk')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('no_nota')
                    ->searchable(),
                TextColumn::make('penanggung_jawab')
                    ->searchable(),
                TextColumn::make('penerima')
                    ->searchable(),
                TextColumn::make('satpam')
                    ->searchable(),
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
                Action::make('print')
                    ->label('Cetak Nota')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn($record) => route('nota-kayu.show', $record))
                    ->openUrlInNewTab(), // ini penting untuk buka tab baru
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
