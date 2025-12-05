<?php

namespace App\Filament\Resources\RencanaKerjaHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

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
                        Log::info('[FORM] jenis_barang_id diubah — reset semua field dependent.');

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
                    ->options(
                        fn(callable $get) =>
                        $get('jenis_barang_id')
                        ? Grade::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                            $q->where('id_jenis_barang', $get('jenis_barang_id'));
                        })
                            ->orderBy('nama_grade')
                            ->pluck('nama_grade', 'id')
                        : []
                    )
                    ->afterStateUpdated(function (callable $set) {
                        Log::info('[FORM] id_grade diubah — reset ukuran & barang_setengah_jadi.');

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
                    ->options(
                        fn(callable $get) =>
                        (!$get('jenis_barang_id') || !$get('id_grade'))
                        ? []
                        : Ukuran::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                            $q->where('id_jenis_barang', $get('jenis_barang_id'))
                                ->where('id_grade', $get('id_grade'));
                        })
                            ->pluck('nama_ukuran', 'id')
                    )
                    ->afterStateUpdated(function (callable $get, callable $set) {

                        Log::info('[FORM] afterStateUpdated(id_ukuran) DIPANGGIL', [
                            'jenis_barang_id' => $get('jenis_barang_id'),
                            'id_grade' => $get('id_grade'),
                            'id_ukuran' => $get('id_ukuran'),
                        ]);

                        if (!$get('jenis_barang_id') || !$get('id_grade') || !$get('id_ukuran')) {
                            Log::warning('[FORM] Kombinasi belum lengkap — reset id_barang_setengah_jadi_hp.');

                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', null);
                            return;
                        }

                        $barang = BarangSetengahJadiHp::where('id_jenis_barang', $get('jenis_barang_id'))
                            ->where('id_grade', $get('id_grade'))
                            ->where('id_ukuran', $get('id_ukuran'))
                            ->with(['jenisBarang', 'grade', 'ukuran'])
                            ->first();

                        Log::info('[FORM] Hasil Query barang_setengah_jadi_hp', [
                            'query_result' => $barang,
                        ]);

                        if ($barang) {
                            Log::info('[FORM] Barang ditemukan, set id_barang_setengah_jadi_hp', [
                                'id_barang_setengah_jadi_hp' => $barang->id,
                            ]);

                            $set('id_barang_setengah_jadi_hp', $barang->id);
                            $set(
                                'barang_setengah_jadi_text',
                                $barang->jenisBarang->nama_jenis_barang . ' | ' .
                                $barang->grade->nama_grade . ' | ' .
                                $barang->ukuran->nama_ukuran
                            );
                        } else {
                            Log::error('[FORM] KOMBINASI TIDAK DITEMUKAN!');

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
                // DISPLAY ONLY
                // ======================
                TextInput::make('barang_setengah_jadi_text')
                    ->label('Barang Setengah Jadi')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                // ======================
                // FIELD YANG DISIMPAN
                // ======================
                TextInput::make('id_barang_setengah_jadi_hp')
                    ->dehydrated(true)
                    ->visible(false)
                    ->required(),
            ]);
    }
}
