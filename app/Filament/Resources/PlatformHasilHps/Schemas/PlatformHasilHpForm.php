<?php

namespace App\Filament\Resources\PlatformHasilHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use App\Models\BarangSetengahJadiHp;
use App\Models\JenisBarang;
use App\Models\Grade;
use App\Models\Mesin;

class PlatformHasilHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                /*
                |------------------------------------------------------------------
                | FILTER GRADE (OPTIONAL)
                |------------------------------------------------------------------
                */
                Select::make('grade_id')
                    ->label('Grade')
                    ->options(
                        Grade::with('kategoriBarang')
                            ->orderBy('id_kategori_barang')
                            ->orderBy('nama_grade')
                            ->get()
                            ->mapWithKeys(fn ($g) => [
                                $g->id =>
                                    ($g->kategoriBarang?->nama_kategori ?? 'Tanpa Kategori')
                                    . ' | ' . $g->nama_grade
                            ])
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Grade')
                    ->dehydrated(false),

                /*
                |------------------------------------------------------------------
                | FILTER JENIS BARANG
                |------------------------------------------------------------------
                */
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(
                        JenisBarang::orderBy('nama_jenis_barang')
                            ->pluck('nama_jenis_barang', 'id')
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Jenis Barang')
                    ->dehydrated(false),

                /*
                |------------------------------------------------------------------
                | BARANG SETENGAH JADI (HASIL FILTER)
                |------------------------------------------------------------------
                */
                Select::make('id_barang_setengah_jadi')
                    ->label('Barang Setengah Jadi')
                    ->required()
                    ->searchable()
                    ->columnSpanFull()
                    ->options(function (callable $get) {

                        $query = BarangSetengahJadiHp::query()
                            ->with(['ukuran', 'jenisBarang', 'grade.kategoriBarang']);

                        if ($get('grade_id')) {
                            $query->where('id_grade', $get('grade_id'));
                        }

                        if ($get('jenis_barang_id')) {
                            $query->where('id_jenis_barang', $get('jenis_barang_id'));
                        }

                        if (!$get('grade_id') && !$get('jenis_barang_id')) {
                            $query->limit(50);
                        }

                        return $query
                            ->orderBy('id', 'desc')
                            ->get()
                            ->mapWithKeys(function ($b) {

                                $kategori = $b->grade?->kategoriBarang?->nama_kategori ?? 'Kategori?';
                                $ukuran   = $b->ukuran?->nama_ukuran ?? 'Ukuran?';
                                $grade    = $b->grade?->nama_grade ?? 'Grade?';
                                $jenis    = $b->jenisBarang?->nama_jenis_barang ?? 'Jenis?';

                                return [
                                    $b->id => "{$kategori} | {$ukuran} | {$grade} | {$jenis}"
                                ];
                            });
                    }),

                /*
                |------------------------------------------------------------------
                | MESIN
                |------------------------------------------------------------------
                */
                Select::make('id_mesin')
                    ->label('Mesin Hotpress')
                    ->options(
                        Mesin::whereHas('kategoriMesin', fn ($q) =>
                            $q->where('nama_kategori_mesin', 'HOTPRESS')
                        )
                        ->orderBy('nama_mesin')
                        ->pluck('nama_mesin', 'id')
                    )
                    ->searchable()
                    ->required(),

                TextInput::make('isi')
                    ->label('Isi')
                    ->numeric()
                    ->required(),

                TextInput::make('no_palet')
                    ->label('Nomor Palet')
                    ->numeric()
                    ->required(),
            ]);
    }
}
