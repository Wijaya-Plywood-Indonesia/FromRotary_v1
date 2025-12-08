<?php

namespace App\Filament\Resources\ModalSandings\Schemas;

use App\Models\BarangSetengahJadiHp;
use App\Models\Grade;
use App\Models\JenisBarang;
use App\Models\KategoriBarang;
use App\Models\ProduksiSanding;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ModalSandingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // FILTER GRADE
                Select::make('grade_id')
                    ->label('Grade')
                    ->options(
                        Grade::with('kategoriBarang')
                            ->get()
                            ->mapWithKeys(fn($g) => [
                                $g->id => ($g->kategoriBarang?->nama_kategori ?? 'Tanpa Kategori')
                                    . ' - ' . $g->nama_grade
                            ])
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Grade'),

                // FILTER JENIS BARANG
                Select::make('id_jenis_barang')
                    ->label('Jenis Barang')
                    ->options(
                        JenisBarang::pluck('nama_jenis_barang', 'id')
                    )
                    ->reactive()
                    ->searchable()
                    ->placeholder('Semua Jenis Barang'),

                // BARANG SETENGAH JADI (DIPENGARUHI FILTER)
                Select::make('id_barang_setengah_jadi')
                    ->label('Barang Setengah Jadi')
                    ->options(function (callable $get) {

                        $query = BarangSetengahJadiHp::query()
                            ->with(['ukuran', 'jenisBarang', 'grade.kategoriBarang']);

                        // FILTER BY GRADE
                        if ($get('grade_id')) {
                            $query->where('id_grade', $get('grade_id'));
                        }

                        // FILTER BY JENIS BARANG
                        if ($get('jenis_barang_id')) {
                            $query->where('id_jenis_barang', $get('jenis_barang_id'));
                        }

                        // Batasi jika tidak ada filter
                        if (!$get('grade_id') && !$get('jenis_barang_id')) {
                            $query->limit(50);
                        }

                        return $query->orderBy('id', 'desc')
                            ->get()
                            ->mapWithKeys(function ($b) {

                                $kategori = $b->grade?->kategoriBarang?->nama_kategori ?? 'Kategori?';
                                $ukuran = $b->ukuran?->dimensi ?? 'Ukuran?';
                                $jenis = $b->jenisBarang?->nama_jenis_barang ?? 'Jenis?';
                                $grade = $b->grade?->nama_grade ?? 'Grade?';

                                return [
                                    $b->id => "{$kategori} — {$ukuran} — {$grade} — {$jenis}"
                                ];
                            });
                    })
                    ->searchable()
                    ->placeholder('Pilih Barang'),

                TextInput::make('kuantitas')
                    ->label('Kuantitas')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('jumlah_sanding')
                    ->label('Jumlah Sanding (Pass)')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('no_palet')
                    ->label('No Palet')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}
