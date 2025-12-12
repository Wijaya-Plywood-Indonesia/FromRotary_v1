<?php

namespace App\Filament\Resources\HasilSandings\Schemas;

use App\Models\BarangSetengahJadiHp;
use App\Models\Grade;
use App\Models\JenisBarang;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HasilSandingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                    // OPTIONS saat create / filter
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

                    // LABEL saat EDIT (ini yang kamu butuhkan!)
                    ->getOptionLabelUsing(function ($value) {
                        $b = BarangSetengahJadiHp::with(['ukuran', 'jenisBarang', 'grade.kategoriBarang'])
                            ->find($value);

                        if (!$b)
                            return $value; // fallback ID
            
                        $kategori = $b->grade?->kategoriBarang?->nama_kategori ?? 'Kategori?';
                        $ukuran = $b->ukuran?->dimensi ?? 'Ukuran?';
                        $jenis = $b->jenisBarang?->nama_jenis_barang ?? 'Jenis?';
                        $grade = $b->grade?->nama_grade ?? 'Grade?';

                        return "{$kategori} — {$ukuran} — {$grade} — {$jenis}";
                    })

                    ->searchable()
                    ->placeholder('Pilih Barang'),
                TextInput::make('kuantitas')
                    ->numeric()
                    ->required(),

                TextInput::make('jumlah_sanding_face')
                    ->label('Jumlah Sanding Face (Pass)')
                    ->numeric()
                    ->minValue(1)

                    ->required(),
                TextInput::make('jumlah_sanding_back')
                    ->label('Jumlah Sanding Back (Pass)')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('no_palet')
                    ->numeric()
                    ->required(),

                Select::make('status')
                    ->options([
                        'Selesai 1 Sisi' => 'Selesai 1 Sisi',
                        'Selesai 2 Sisi' => 'Selesai 2 Sisi',
                        'Belum Selesai' => 'Belum Selesai',
                    ])
                    ->default('Belum Selesai')
                    ->required()
                    ->label('Status'),
            ]);
    }
}
