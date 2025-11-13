<?php

namespace App\Filament\Resources\DetailMasuks\Schemas;

use Filament\Schemas\Schema;
use App\Models\KayuMasuk;
use App\Models\JenisKayu;
use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
class DetailMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_palet')
                    ->label('No. Palet')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('PLT-001'),

                // KW
                TextInput::make('kw')
                    ->label('KW')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('KW1'),

                // ISI
                TextInput::make('isi')
                    ->label('Isi')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('100'),

                // KAYU MASUK (OPSIONAL)
                Select::make('id_kayu_masuk')
                    ->label('Kayu Masuk')
                    ->relationship('kayuMasuk', 'seri')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih Kayu Masuk')
                    ->nullable(),

                // JENIS KAYU (OPSIONAL)
                Select::make('id_jenis_kayu')
                    ->label('Jenis Kayu')
                    ->relationship('jenisKayu', 'nama_kayu')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih Jenis Kayu')
                    ->nullable(),

                // PRODUKSI DRYER (WAJIB)
                Select::make('id_produksi_dryer')
                    ->label('Produksi Dryer')
                    ->relationship('produksiDryer', 'tanggal_produksi')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->tanggal_produksi . ' | ' . $record->shift
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih Produksi Dryer'),
            ]);
    }
}
