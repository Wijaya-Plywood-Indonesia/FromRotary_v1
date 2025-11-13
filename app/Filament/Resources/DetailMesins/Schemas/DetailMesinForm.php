<?php

namespace App\Filament\Resources\DetailMesins\Schemas;

use Filament\Schemas\Schema;
use App\Models\KategoriMesin;
use App\Models\Mesin;
use App\Models\ProduksiPressDryer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DetailMesinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_kategori_mesin')
                    ->label('Kategori Mesin')
                    ->options(KategoriMesin::pluck('nama_kategori_mesin', 'id'))
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->placeholder('Pilih Kategori'),

                // 2. MESIN (FILTER BERDASARKAN KATEGORI)
                Select::make('id_mesin')
                    ->label('Mesin')
                    ->options(
                        fn($get) =>
                        $get('id_kategori_mesin')
                        ? Mesin::where('kategori_mesin_id', $get('id_kategori_mesin'))
                            ->get()
                            ->mapWithKeys(fn($m) => [
                                $m->id => $m->nama_mesin . ' (' . $m->kategoriMesin?->nama_kategori_mesin . ')'
                            ])
                            ->toArray()
                        : []
                    )
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->placeholder('Pilih Mesin'),

                // 3. JAM KERJA
                TextInput::make('jam_kerja_mesin')
                    ->label('Jam Kerja Mesin')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('8 Jam')
                    ->helperText('Contoh: 8 Jam, 6.5 Jam'),

                // 4. PRODUKSI (PAKAI RELATIONSHIP)
                Select::make('id_produksi_dryer')
                    ->label('Produksi Dryer')
                    ->relationship('produksiDryer', 'tanggal_produksi')
                    ->getOptionLabelFromRecordUsing(
                        fn($r) =>
                        $r->tanggal_produksi->format('d M Y') . ' | ' . $r->shift
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
