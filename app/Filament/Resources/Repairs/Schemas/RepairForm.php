<?php

namespace App\Filament\Resources\Repairs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class RepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_repair')
                    ->label('Tanggal Repair')
                    ->required()
                    ->default(today())           // otomatis hari ini
                    ->maxDate(today())           // tidak boleh masa depan
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->native(false),

                Select::make('id_ukuran')
                    ->label('Ukuran Kayu')
                    ->relationship('ukuran', 'panjang') // relasi di model Repair
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        "{$record->panjang} × {$record->lebar} × {$record->tebal} cm"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->relationship('jenisKayu', 'kode_kayu') // relasi di model Repair
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('kualitas')
                    ->label('Kualitas')
                    ->options([
                        'KW1' => 'KW1',
                        'KW2' => 'KW2',
                        'KW3' => 'KW3',
                        'KW4' => 'KW4',
                    ])
                    ->required()
                    ->searchable(false),

                TextInput::make('total_lembar')
                    ->label('Total Lembar')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                TimePicker::make('jam_kerja')
                    ->label('Jam Kerja')
                    ->required(),

                TextInput::make('target')
                    ->label('Target')
                    ->required()
                    ->numeric(),
            ]);
    }
}
