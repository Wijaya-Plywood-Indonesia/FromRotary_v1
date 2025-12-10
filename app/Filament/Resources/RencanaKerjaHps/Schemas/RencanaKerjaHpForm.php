<?php

namespace App\Filament\Resources\RencanaKerjaHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

use App\Models\JenisBarang;
use App\Models\Grade;
use App\Models\Ukuran;
use App\Models\BarangSetengahJadiHp;
use App\Models\KategoriBarang;

class RencanaKerjaHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                /*
                |--------------------------------------------------------------------------
                | KATEGORI BARANG (UI ONLY)
                |--------------------------------------------------------------------------
                | ❌ Tidak trigger apa pun
                | ❌ Tidak disimpan ke DB
                */
                Select::make('kategori_barang_ui')
                    ->label('Kategori Barang')
                    ->options(
                        KategoriBarang::orderBy('nama_kategori')
                            ->pluck('nama_kategori', 'id')
                    )
                    ->placeholder('Pilih kategori')
                    ->dehydrated(false)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | JENIS BARANG
                |--------------------------------------------------------------------------
                */
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(
                        JenisBarang::orderBy('nama_jenis_barang')
                            ->pluck('nama_jenis_barang', 'id')
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        Log::info('[FORM] jenis_barang_id berubah - reset dependensi');

                        $set('id_grade', null);
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                    })
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | GRADE (TIDAK TERGANTUNG KATEGORI)
                |--------------------------------------------------------------------------
                */
                Select::make('id_grade')
                    ->label('Grade')
                    ->reactive()
                    ->options(
                        fn (callable $get) =>
                            $get('jenis_barang_id')
                                ? Grade::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                                    $q->where('id_jenis_barang', $get('jenis_barang_id'));
                                })
                                ->orderBy('nama_grade')
                                ->pluck('nama_grade', 'id')
                                : []
                    )
                    ->afterStateUpdated(function (callable $set) {
                        Log::info('[FORM] id_grade berubah - reset ukuran');

                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                    })
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | UKURAN
                |--------------------------------------------------------------------------
                */
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->reactive()
                    ->searchable()
                    ->options(
                        fn (callable $get) =>
                            (!$get('jenis_barang_id') || !$get('id_grade'))
                                ? []
                                : Ukuran::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                                    $q->where('id_jenis_barang', $get('jenis_barang_id'))
                                      ->where('id_grade', $get('id_grade'));
                                })
                                ->get() 
                        ->mapWithKeys(fn ($u) => [ $u->id => $u->nama_ukuran ]),
                    )
                    ->afterStateUpdated(function (callable $get, callable $set) {

                        if (
                            !$get('jenis_barang_id') ||
                            !$get('id_grade') ||
                            !$get('id_ukuran')
                        ) {
                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', null);
                            return;
                        }

                        $barang = BarangSetengahJadiHp::where('id_jenis_barang', $get('jenis_barang_id'))
                            ->where('id_grade', $get('id_grade'))
                            ->where('id_ukuran', $get('id_ukuran'))
                            ->with(['jenisBarang', 'grade', 'ukuran'])
                            ->first();

                        if ($barang) {
                            $set('id_barang_setengah_jadi_hp', $barang->id);
                            $set(
                                'barang_setengah_jadi_text',
                                $barang->jenisBarang->nama_jenis_barang . ' | ' .
                                $barang->grade->nama_grade . ' | ' .
                                $barang->ukuran->nama_ukuran
                            );
                        } else {
                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', '⚠ Kombinasi tidak terdaftar');
                        }
                    })
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | JUMLAH
                |--------------------------------------------------------------------------
                */
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | PREVIEW BARANG SETENGAH JADI
                |--------------------------------------------------------------------------
                */
                TextInput::make('barang_setengah_jadi_text')
                    ->label('Barang Setengah Jadi')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | HIDDEN ID BARANG SETENGAH JADI (KUNCI)
                |--------------------------------------------------------------------------
                */
                Hidden::make('id_barang_setengah_jadi_hp')
                    ->required()
                    ->dehydrated(true),
            ]);
    }
}
