<?php

namespace App\Filament\Resources\ModalSandings\Schemas;

use App\Models\BarangSetengahJadiHp;
use App\Models\Grade;
use App\Models\JenisBarang;
use App\Models\ModalSanding;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModalSandingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | FILTER GRADE
            |--------------------------------------------------------------------------
            */
            Select::make('grade_id')
                ->label('Grade')
                ->default(fn(callable $get) => self::lastValue($get, 'grade_id'))
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

            /*
            |--------------------------------------------------------------------------
            | FILTER JENIS BARANG
            |--------------------------------------------------------------------------
            */
            Select::make('id_jenis_barang')
                ->label('Jenis Barang')
                ->default(fn(callable $get) => self::lastValue($get, 'id_jenis_barang'))
                ->options(JenisBarang::pluck('nama_jenis_barang', 'id'))
                ->reactive()
                ->searchable()
                ->placeholder('Semua Jenis Barang'),

            /*
            |--------------------------------------------------------------------------
            | BARANG SETENGAH JADI (TERGANTUNG FILTER)
            |--------------------------------------------------------------------------
            */
            Select::make('id_barang_setengah_jadi')
                ->label('Barang Setengah Jadi')
                ->default(fn(callable $get) => self::lastValue($get, 'id_barang_setengah_jadi'))
                ->options(function (callable $get) {

                    $query = BarangSetengahJadiHp::query()
                        ->with(['ukuran', 'jenisBarang', 'grade.kategoriBarang']);

                    if ($get('grade_id')) {
                        $query->where('id_grade', $get('grade_id'));
                    }

                    if ($get('id_jenis_barang')) {
                        $query->where('id_jenis_barang', $get('id_jenis_barang'));
                    }

                    if (!$get('grade_id') && !$get('id_jenis_barang')) {
                        $query->limit(50);
                    }

                    return $query->orderBy('id', 'desc')
                        ->get()
                        ->mapWithKeys(function ($b) {
                            $kategori = $b->grade?->kategoriBarang?->nama_kategori ?? 'Kategori?';
                            $ukuran = $b->ukuran?->dimensi ?? 'Ukuran?';
                            $grade = $b->grade?->nama_grade ?? 'Grade?';
                            $jenis = $b->jenisBarang?->nama_jenis_barang ?? 'Jenis?';

                            return [
                                $b->id => "{$kategori} — {$ukuran} — {$grade} — {$jenis}",
                            ];
                        });
                })
                ->searchable()
                ->placeholder('Pilih Barang'),

            /*
            |--------------------------------------------------------------------------
            | KUANTITAS
            |--------------------------------------------------------------------------
            */
            TextInput::make('kuantitas')
                ->label('Kuantitas')
                ->numeric()
                ->minValue(1)
                ->default(fn(callable $get) => self::lastValue($get, 'kuantitas'))
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | JUMLAH PASS SANDING
            |--------------------------------------------------------------------------
            */
            TextInput::make('jumlah_sanding')
                ->label('Jumlah Sanding (Pass)')
                ->numeric()
                ->minValue(1)
                ->default(fn(callable $get) => self::lastValue($get, 'jumlah_sanding'))
                ->required(),

            /*
            |--------------------------------------------------------------------------
            | NO PALET + VALIDASI UNIQUE
            |--------------------------------------------------------------------------
            */
            TextInput::make('no_palet')
                ->label('No Palet')
                ->numeric()
                ->default(fn(callable $get) => self::lastValue($get, 'no_palet'))
                ->required()
                ->rule(function (callable $get) {
                    return function ($attribute, $value, $fail) use ($get) {

                        $idBarang = $get('id_barang_setengah_jadi');
                        $idProduksi = $get('id_produksi_sanding');

                        if (!$idBarang || !$idProduksi) {
                            return;
                        }

                        $exists = ModalSanding::where('id_produksi_sanding', $idProduksi)
                            ->where('id_barang_setengah_jadi', $idBarang)
                            ->where('no_palet', $value)
                            ->exists();

                        if ($exists) {
                            $fail('Nomor palet ini sudah digunakan untuk kombinasi tersebut.');
                        }
                    };
                }),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: membaca data terakhir per id_produksi_sanding
    |--------------------------------------------------------------------------
    */
    private static function lastValue(callable $get, string $column)
    {
        $idProduksi = $get('id_produksi_sanding');
        if (!$idProduksi) {
            return null;
        }

        return ModalSanding::where('id_produksi_sanding', $idProduksi)
            ->latest('id')
            ->value($column);
    }
}
