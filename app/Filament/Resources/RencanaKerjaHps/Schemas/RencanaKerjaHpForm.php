<?php

namespace App\Filament\Resources\RencanaKerjaHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

// Model
use App\Models\JenisBarang;
use App\Models\Grade;
use App\Models\Ukuran;
use App\Models\BarangSetengahJadiHp;

class RencanaKerjaHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                // ======================
                // ROW 1 - JENIS BARANG
                // ======================
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(
                        JenisBarang::orderBy('nama_jenis_barang')
                            ->pluck('nama_jenis_barang', 'id')
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_grade', null);
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                    })
                    ->required(),

                // ======================
                // ROW 1 - GRADE
                // ======================
                Select::make('id_grade')
                    ->label('Grade')
                    ->reactive()
                    ->options(fn (callable $get) =>
                        $get('jenis_barang_id')
                            ? Grade::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                                $q->where('id_jenis_barang', $get('jenis_barang_id'));
                            })
                            ->orderBy('nama_grade')
                            ->pluck('nama_grade', 'id')
                            : []
                    )
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                    })
                    ->required(),

                // ======================
                // ROW 2 - UKURAN
                // ======================
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->reactive()
                    ->searchable()
                    ->options(fn (callable $get) =>
                        (!$get('jenis_barang_id') || !$get('id_grade'))
                            ? []
                            : Ukuran::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                                $q->where('id_jenis_barang', $get('jenis_barang_id'))
                                  ->where('id_grade', $get('id_grade'));
                            })
                            ->get()
                            ->mapWithKeys(fn ($u) => [
                                $u->id => $u->nama_ukuran
                            ])
                    )
                    ->afterStateUpdated(function (callable $get, callable $set) {

                        if (!$get('jenis_barang_id') || !$get('id_grade') || !$get('id_ukuran')) {
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
                            $set('barang_setengah_jadi_text',
                                $barang->jenisBarang->nama_jenis_barang . ' | ' .
                                $barang->grade->nama_grade . ' | ' .
                                $barang->ukuran->nama_ukuran
                            );
                        } else {
                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', '⚠ KOMBINASI TIDAK TERDAFTAR');
                        }
                    })
                    ->required(),

                // ======================
                // ROW 2 - JUMLAH
                // ======================
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),

                // ======================
                // ROW 3 - BARANG SETENGAH JADI (DISPLAY)
                // ======================
                TextInput::make('barang_setengah_jadi_text')
                    ->label('Barang Setengah Jadi')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                // ======================
                // HIDDEN FIELD ID (WAJIB DISIMPAN)
                // ======================
                TextInput::make('id_barang_setengah_jadi_hp')
                    ->dehydrated(true)
                    ->visible(false)
                    ->required(),
            ]);
    }
}
