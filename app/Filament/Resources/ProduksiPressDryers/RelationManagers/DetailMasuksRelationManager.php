<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;


use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Ukuran;

class DetailMasuksRelationManager extends RelationManager
{
    protected static string $relationship = 'detailMasuks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('no_palet')
                    ->label('Nomor Palet')
                    ->required()
                    ->maxLength(255),

                // Relasi ke Jenis Kayu
                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->relationship('jenisKayu', 'nama_kayu') // Asumsi: Model JenisKayu punya kolom 'nama'
                    ->searchable()
                    ->preload()
                    ->required(),

                // Relasi ke Kayu Masuk (Optional)
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->relationship('ukuran', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nama_ukuran)
                    ->searchable()
                    ->preload()
                    ->getSearchResultsUsing(function ($query) {
                        return Ukuran::where('panjang', 'like', "%{$query}%")
                            ->orWhere('lebar', 'like', "%{$query}%")
                            ->orWhere('tebal', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(fn ($item) => [$item->id => $item->nama_ukuran]);
                    })
                    ->nullable(), // Sesuai dengan migrasi

                TextInput::make('kw')
                    ->label('KW (Kualitas)')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Cth: 1, 2, 3,dll.'),

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
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('Ukuran.nama_ukuran')
                    ->label('Ukuran')
                    ->searchable(false)
                    ->placeholder('Ukuran'),

                TextColumn::make('kw')
                    ->label('Kualitas (KW)')
                    ->searchable(),

                TextColumn::make('isi')
                    ->label('Isi'),

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
