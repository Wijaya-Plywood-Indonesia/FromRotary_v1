<?php

namespace App\Filament\Resources\PlatformHasilHps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use App\Models\JenisBarang;
use App\Models\Grade;
use App\Models\Ukuran;
use App\Models\BarangSetengahJadiHp;
use Illuminate\Database\Eloquent\Builder;

class PlatformHasilHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Hidden::make('last_rencana')
                    ->default(function ($livewire) {
                        return $livewire->ownerRecord
                            ->rencanaKerjaHp()
                            ->latest()
                            ->with('barangSetengahJadiHp')
                            ->first();
                    })
                    ->dehydrated(false),

                /*
                 * ==========================
                 * JENIS BARANG
                 * ==========================
                 */
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(JenisBarang::pluck('nama_jenis_barang', 'id'))
                    ->default(fn ($livewire) =>
                        optional(
                            $livewire->ownerRecord
                                ->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_jenis_barang
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_grade', null);
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                        $set('id_barang_setengah_jadi', null);
                    })
                    ->required(),

                /*
                 * ==========================
                 * GRADE
                 * ==========================
                 */
                Select::make('id_grade')
                    ->label('Grade')
                    ->options(function (callable $get) {
                        if (!$get('jenis_barang_id')) return [];

                        return Grade::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                            $q->where('id_jenis_barang', $get('jenis_barang_id'));
                        })->pluck('nama_grade', 'id');
                    })
                    ->default(fn ($livewire) =>
                        optional(
                            $livewire->ownerRecord
                                ->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_grade
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi_hp', null);
                        $set('barang_setengah_jadi_text', null);
                        $set('id_barang_setengah_jadi', null);
                    })
                    ->required(),

                /*
                 * ==========================
                 * UKURAN
                 * ==========================
                 */
                Select::make('id_ukuran')
                    ->label('Ukuran')
                    ->searchable()
                    ->options(function (callable $get) {
                        if (!$get('jenis_barang_id') || !$get('id_grade')) return [];

                        return Ukuran::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                            $q->where('id_jenis_barang', $get('jenis_barang_id'))
                              ->where('id_grade', $get('id_grade'));
                        })
                            ->get()
                            ->mapWithKeys(fn ($u) => [
                                $u->id => $u->nama_ukuran
                            ]);
                    })
                    ->default(fn ($livewire) =>
                        optional(
                            $livewire->ownerRecord
                                ->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_ukuran
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {

                        if (!$get('jenis_barang_id') || !$get('id_grade') || !$get('id_ukuran')) {
                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', null);
                            $set('id_barang_setengah_jadi', null);
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
                            
                            // Set ID pada hidden field saat ada interaksi
                            $set('id_barang_setengah_jadi', $barang->id); 
                        } else {
                            $set('id_barang_setengah_jadi_hp', null);
                            $set('barang_setengah_jadi_text', '⚠ KOMBINASI TIDAK TERDAFTAR');
                            $set('id_barang_setengah_jadi', null);
                        }
                    })
                    ->required(),

                /*
                 * TEXT READ ONLY
                 */
                TextInput::make('barang_setengah_jadi_text')
                    ->label('Barang Setengah Jadi')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->afterStateHydrated(function (callable $set, $livewire) {

                        if (!$livewire->ownerRecord) {
                            return;
                        }

                        $last = $livewire->ownerRecord
                            ->rencanaKerjaHp()
                            ->latest()
                            ->with([
                                'barangSetengahJadiHp.jenisBarang',
                                'barangSetengahJadiHp.grade',
                                'barangSetengahJadiHp.ukuran',
                            ])
                            ->first();

                        if (!$last || !$last->barangSetengahJadiHp) {
                            return;
                        }

                        $b = $last->barangSetengahJadiHp;
                        
                        // ✅ SET TEXT
                        $set(
                            'barang_setengah_jadi_text',
                            $b->jenisBarang->nama_jenis_barang . ' | ' .
                            $b->grade->nama_grade . ' | ' .
                            $b->ukuran->nama_ukuran
                        );
                    }),

                Hidden::make('id_barang_setengah_jadi')
                    ->required()
                    ->dehydrated(true)
                    // 💡 SOLUSI FINAL: Set nilai ID tepat sebelum disubmit (dehydrate)
                    ->dehydrateStateUsing(function (callable $get) {
                        // Jika nilai sudah ada (karena user select ulang), kembalikan nilai tersebut
                        if ($get('id_barang_setengah_jadi')) {
                            return $get('id_barang_setengah_jadi');
                        }

                        // Jika nilai masih null (karena tidak select ulang), cari ID-nya
                        $jenisBarangId = $get('jenis_barang_id');
                        $idGrade = $get('id_grade');
                        $idUkuran = $get('id_ukuran');

                        if ($jenisBarangId && $idGrade && $idUkuran) {
                            $barang = BarangSetengahJadiHp::where('id_jenis_barang', $jenisBarangId)
                                ->where('id_grade', $idGrade)
                                ->where('id_ukuran', $idUkuran)
                                ->first();

                            return $barang ? $barang->id : null;
                        }

                        return null; // Akan gagal validasi required jika null
                    }),

                /*
                 * FIELD LAIN
                 */
                Select::make('id_mesin')
                    ->label('Mesin Hotpress')
                    ->options(
                        \App\Models\Mesin::whereHas('kategoriMesin', function ($q) {
                            $q->where('nama_kategori_mesin', 'HOTPRESS');
                        })
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