<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use app\Models\Mesin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class DetailMesinsRelationManager extends RelationManager
{
        protected static ?string $title = 'Mesin Dryer';
    protected static string $relationship = 'detailMesins';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('id_mesin_dryer')
                    ->label('Mesin Dryer')
                    // Asumsi: relasi 'mesinDryer' & kolom 'nama' atau 'kode_mesin'
                    ->relationship('mesin', 'nama_mesin')
                    ->searchable()
                    ->preload()
                    ->nullable(), // Sesuai dengan migrasi Anda

                TextInput::make('jam_kerja_mesin')
                    ->default(12)        // otomatis 12
                    ->hidden()           // tidak tampil di form
                    ->dehydrated(),    // tetap disimpan ke database
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mesin.nama_mesin') // Asumsi: relasi 'mesinDryer' & kolom 'nama'
                    ->label('Mesin Dryer')
                    ->searchable()
                    ->placeholder('N/A'), // Teks jika mesin tidak dipilih (nullable)

                TextColumn::make('jam_kerja_mesin')
                    ->label('Jam Kerja Mesin')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan by default
            ])
            ->filters([
                // Tempat filter jika Anda membutuhkannya
            ])
            ->headerActions([
                // INI ADALAH TOMBOL UNTUK MEMBUAT DATA BARU
                CreateAction::make(),
            ])
            ->recordActions([
                // Tombol di setiap baris (Edit, Delete)
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
