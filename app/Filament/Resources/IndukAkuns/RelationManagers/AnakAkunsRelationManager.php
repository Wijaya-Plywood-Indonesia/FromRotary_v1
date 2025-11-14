<?php

namespace App\Filament\Resources\IndukAkuns\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnakAkunsRelationManager extends RelationManager
{
    protected static string $relationship = 'AnakAkuns';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_anak_akun')
                    ->label('Kode Anak Akun')
                    ->maxLength(50)
                    ->required(),

                TextInput::make('nama_anak_akun')
                    ->label('Nama Anak Akun')
                    ->required()
                    ->maxLength(255),

                Textarea::make('keterangan')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_anak_akun')
            ->columns([
                TextColumn::make('kode_anak_akun')
                    ->label('Kode Akun')
                    ->getStateUsing(function ($record) {
                        return "{$record->indukAkun->kode_induk_akun}{$record->kode_anak_akun}";
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_anak_akun')
                    ->label('Nama Anak Akun')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->limit(30)
                    ->suffix('...')
                    ->toggleable(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
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
