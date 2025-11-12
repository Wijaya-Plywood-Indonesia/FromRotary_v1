<?php

namespace App\Filament\Resources\DetailMesins\Schemas;

use Filament\Schemas\Schema;
use App\Models\MesinDryer;
use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DetailMesinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_mesin_dryer')
                    ->label('Mesin Dryer')
                    ->relationship('mesinDryer', 'nama_mesin') // asumsi kolom: nama_mesin
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih Mesin')
                    ->nullable(),

                // JAM KERJA MESIN
                TextInput::make('jam_kerja_mesin')
                    ->label('Jam Kerja Mesin')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('8 Jam')
                    ->helperText('Contoh: 8 Jam, 6.5 Jam'),

                // PRODUKSI DRYER (WAJIB)
                Select::make('id_produksi_dryer')
                    ->label('Produksi Dryer')
                    ->relationship('produksiDryer', 'tanggal_produksi')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->tanggal_produksi->format('d M Y') . ' | ' . $record->shift
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih Produksi'),
            ]);
    }
}
