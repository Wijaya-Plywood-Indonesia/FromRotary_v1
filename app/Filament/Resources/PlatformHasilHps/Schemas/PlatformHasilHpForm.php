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
use App\Models\Mesin;
use Illuminate\Database\Eloquent\Builder;

class PlatformHasilHpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                /*
                 |--------------------------------------------------------------------------
                 | JENIS BARANG
                 |--------------------------------------------------------------------------
                 */
                Select::make('jenis_barang_id')
                    ->label('Jenis Barang')
                    ->options(JenisBarang::pluck('nama_jenis_barang', 'id'))
                    ->default(fn ($livewire) =>
                        optional(
                            $livewire->ownerRecord
                                ?->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_jenis_barang
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_grade', null);
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi', null);
                        $set('barang_setengah_jadi_text', null);
                    })
                    ->required(),

                /*
                 |--------------------------------------------------------------------------
                 | GRADE
                 |--------------------------------------------------------------------------
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
                                ?->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_grade
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('id_ukuran', null);
                        $set('id_barang_setengah_jadi', null);
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
                    ->searchable()
                    ->options(function (callable $get) {
                        if (!$get('jenis_barang_id') || !$get('id_grade')) {
                            return [];
                        }

                        return Ukuran::whereHas('barangSetengahJadiHp', function (Builder $q) use ($get) {
                            $q->where('id_jenis_barang', $get('jenis_barang_id'))
                              ->where('id_grade', $get('id_grade'));
                        })
                        ->get() 
                        ->mapWithKeys(fn ($u) => [ $u->id => $u->nama_ukuran ]);
                    })
                    ->default(fn ($livewire) =>
                        optional(
                            $livewire->ownerRecord
                                ?->rencanaKerjaHp()
                                ->latest()
                                ->with('barangSetengahJadiHp')
                                ->first()
                        )->barangSetengahJadiHp?->id_ukuran
                    )
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {

                        if (!$get('jenis_barang_id') || !$get('id_grade') || !$get('id_ukuran')) {
                            $set('id_barang_setengah_jadi', null);
                            $set('barang_setengah_jadi_text', null);
                            return;
                        }

                        $barang = BarangSetengahJadiHp::where('id_jenis_barang', $get('jenis_barang_id'))
                            ->where('id_grade', $get('id_grade'))
                            ->where('id_ukuran', $get('id_ukuran'))
                            ->with(['jenisBarang', 'grade', 'ukuran'])
                            ->first();

                        if ($barang) {
                            $set('id_barang_setengah_jadi', $barang->id);

                            $set(
                                'barang_setengah_jadi_text',
                                "{$barang->jenisBarang->nama_jenis_barang} | {$barang->grade->nama_grade} | {$barang->ukuran->nama_ukuran}"
                            );
                        } else {
                            $set('id_barang_setengah_jadi', null);
                            $set('barang_setengah_jadi_text', '⚠ KOMBINASI TIDAK TERDAFTAR');
                        }
                    })
                    ->required(),

                /*
                 |--------------------------------------------------------------------------
                 | TEXT VIEW (READ ONLY)
                 |--------------------------------------------------------------------------
                 */
                TextInput::make('barang_setengah_jadi_text')
                    ->label('Barang Setengah Jadi')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->afterStateHydrated(function (callable $set, $livewire) {

                        $last = $livewire->ownerRecord
                            ?->rencanaKerjaHp()
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

                        $set(
                            'barang_setengah_jadi_text',
                            "{$b->jenisBarang->nama_jenis_barang} | {$b->grade->nama_grade} | {$b->ukuran->nama_ukuran}"
                        );
                    }),

                /*
                 |--------------------------------------------------------------------------
                 | HIDDEN ID – ✅ FIX UTAMA
                 |--------------------------------------------------------------------------
                 */
                Hidden::make('id_barang_setengah_jadi')
                    ->required()
                    ->afterStateHydrated(function (callable $set, callable $get, $livewire) {

                        // Jangan override kalau user sudah interaksi
                        if ($get('id_barang_setengah_jadi')) {
                            return;
                        }

                        $last = $livewire->ownerRecord
                            ?->rencanaKerjaHp()
                            ->latest()
                            ->with('barangSetengahJadiHp')
                            ->first();

                        if ($last && $last->barangSetengahJadiHp) {
                            $set('id_barang_setengah_jadi', $last->barangSetengahJadiHp->id);
                        }
                    })
                    ->dehydrated(true),

                /*
                 |--------------------------------------------------------------------------
                 | FIELD LAINNYA
                 |--------------------------------------------------------------------------
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
