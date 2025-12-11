<?php

namespace App\Filament\Resources\BahanRepairs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Ukuran;
use App\Models\JenisKayu;
use Illuminate\Database\Eloquent\Builder;

class BahanRepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_ukuran')
                    ->label('Ukuran Kayu')
                    ->relationship(
                        'ukuran',
                        'nama_ukuran',
                        fn($query) => $query->orderByRaw('panjang * lebar * tebal DESC')
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->dimensi)
                    ->searchable(['nama_ukuran', 'panjang', 'lebar', 'tebal'])
                    ->preload()
                    ->required()
                ,

                Select::make('id_jenis')
                    ->label('Jenis Kayu')
                    ->relationship(
                        'jenisKayu',
                        'nama_kayu',
                        fn($query) => $query->orderBy('kode_kayu')
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->kode_kayu
                        ? "{$record->kode_kayu}"
                        : $record->nama_kayu
                    )
                    ->searchable(['kode_kayu', 'nama_kayu'])
                    ->preload()
                    ->required(),

                Select::make('kw')
                    ->label('KW')
                    ->options([
                        'KW-1' => 'KW-1',
                        'KW-2' => 'KW-2',
                        'KW-3' => 'KW-3',
                        'KW-4' => 'KW-4',
                    ])
                    ->default('KW-1')
                    ->required()
                    ->searchable(false),

                TextInput::make('total_lembar')
                    ->label('Total Lembar')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}
