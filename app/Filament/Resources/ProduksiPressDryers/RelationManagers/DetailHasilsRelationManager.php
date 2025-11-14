<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use App\Filament\Resources\DetailHasils\Schemas\DetailHasilForm;
use App\Filament\Resources\DetailHasils\Tables\DetailHasilsTable;
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
use App\Models\Ukuran;

class DetailHasilsRelationManager extends RelationManager
{
    protected static string $relationship = 'detailHasils';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('no_palet')
                    ->label('Nomor Palet')
                    ->required()
                    ->maxLength(255),

                // Relasi ke Ukuran (id_ukuran)
                Select::make('id_ukuran')
                    ->label('Ukuran Kayu')
                    ->relationship('ukuran', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nama_ukuran)
                    ->searchable()
                    ->preload()
                    ->getSearchResultsUsing(function ($query) {
                        return Ukuran::where('panjang', 'like', "%{$query}%")
                            ->orWhere('lebar', 'like', "%{$query}%")
                            ->orWhere('tebal', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(fn($item) => [$item->id => $item->nama_ukuran]);
                    })
                    ->required(),

                // Relasi ke Jenis Kayu (id_jenis_kayu)
                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->relationship('jenisKayu', 'nama_kayu')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('kw')
                    ->label('Kualitas (KW)')
                    ->numeric()          // memastikan input angka
                    ->rule('integer')    // validasi integer
                    ->required()
                    ->placeholder('Cth: 1, 2, 3 dll.'),

                TextInput::make('isi')
                    ->label('Isi')
                    ->required()
                    ->numeric()
                    ->placeholder('Cth: 1.5 atau 100'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_palet')
                    ->label('No. Palet')
                    ->searchable(),

                TextColumn::make('jenisKayu.nama_kayu')
                    ->label('Jenis Kayu')
                    ->searchable(),

                TextColumn::make('ukuran.nama_ukuran')
                    ->label('Ukuran')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('kw')
                    ->label('Kualitas (KW)')
                    ->searchable(),

                TextColumn::make('isi')
                    ->label('Isi'),

                TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
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
