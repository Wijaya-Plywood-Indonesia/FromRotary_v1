<?php

namespace App\Filament\Resources\Targets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;

class TargetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom status dengan warna





                BadgeColumn::make('status')
                    ->label('Status')
                    ->icons([
                        'heroicon-o-x-circle' => 'diajukan',
                        'heroicon-o-check-circle' => 'disetujui',
                    ])
                    ->colors([
                        'danger' => 'diajukan',  // teks + badge merah
                        'success' => 'disetujui', // teks + badge hijau
                    ]),


                TextColumn::make('mesin.nama_mesin')->label('Mesin'),



                TextColumn::make('jenisKayu.nama_kayu')->label('Jenis Kayu'),

                TextColumn::make('target')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('orang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jam')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('targetperjam')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('targetperorang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gaji')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('potongan')
                    ->numeric()
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
                //
            ])
            ->recordActions([
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
