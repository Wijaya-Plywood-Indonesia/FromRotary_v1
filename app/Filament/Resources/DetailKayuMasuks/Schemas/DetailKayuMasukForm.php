<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

use App\Models\DetailKayuMasuk;
use App\Models\JenisKayu;
use App\Models\Lahan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Facades\Filament;

class DetailKayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns([
            // 💡 Ini kunci untuk layout responsif
            'default' => 3, // di layar kecil (HP)
            'md' => 3,      // di layar sedang (tablet)
            'xl' => 3,      // di layar besar (desktop)
        ])->components([
                    Select::make('id_lahan')
                        ->label('Lahan')
                        ->options(
                            Lahan::query()
                                ->get()
                                ->mapWithKeys(fn($lahan) => [
                                    $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                                ])
                        )
                        ->default(fn() => DetailKayuMasuk::latest('id')->value('id_lahan') ?? 1)
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!$state) {
                                $set('panjang', 0);
                                return;
                            }

                            $lahan = Lahan::find($state);

                            if (!$lahan) {
                                $set('panjang', 0);
                                return;
                            }

                            $nama = strtolower($lahan->nama_lahan ?? '');

                            // Jika nama lahan mengandung angka 130 atau 260
                            if (str_contains($nama, '130')) {
                                $set('panjang', 130);
                                return;
                            } elseif (str_contains($nama, '260')) {
                                $set('panjang', 260);
                                return;
                            }

                            // Jika tidak mengandung angka, ambil panjang terakhir berdasarkan lahan_id
                            $lastPanjang = DetailKayuMasuk::where('id_lahan', $state)
                                ->latest('id')
                                ->value('panjang');

                            $set('panjang', $lastPanjang ?? 0);
                        }),

                    Select::make('grade')
                        ->label('Grade')
                        ->options([
                            1 => 'Grade A',
                            2 => 'Grade B',
                        ])
                        ->required()
                        ->default(1)
                        ->native(false)
                        ->searchable()
                        ->reactive()
                        ->afterStateHydrated(function ($state, $set) {
                            $saved = request()->cookie('filament_local_storage_detail_kayu_masuk.grade')
                                ?? optional(json_decode(request()->header('X-Filament-Local-Storage'), true))['detail_kayu_masuk.grade']
                                ?? null;

                            if ($saved && in_array($saved, [1, 2])) {
                                $set('grade', (int) $saved);
                            }
                        })
                        ->afterStateUpdated(function ($state) {
                            cookie()->queue('filament_local_storage_detail_kayu_masuk.grade', $state, 60 * 24 * 30); // 30 hari
                        }),

                    Select::make('panjang')
                        ->label('Panjang')
                        ->options([
                            130 => '130 cm',
                            260 => '260 cm',
                            0 => 'Tidak Diketahui',
                        ])
                        ->required()
                        ->default(function () {
                            // Saat form pertama dibuka, ambil panjang terakhir dari lahan terakhir
                            $lastLahan = DetailKayuMasuk::latest('id')->value('id_lahan');
                            if (!$lastLahan)
                                return 0;

                            $lastPanjang = DetailKayuMasuk::where('id_lahan', $lastLahan)
                                ->latest('id')
                                ->value('panjang');

                            return $lastPanjang ?? 0;
                        })
                        ->searchable()
                        ->native(false),


                    Select::make('jenis_kayu_id')
                        ->label('Jenis Kayu')
                        ->options(
                            JenisKayu::query()
                                ->get()
                                ->mapWithKeys(fn($JenisKayu) => [
                                    $JenisKayu->id => "{$JenisKayu->kode_kayu} - {$JenisKayu->nama_kayu}",
                                ])
                        )
                        ->default(fn() => DetailKayuMasuk::latest('id')->value('id_jenis_kayu') ?? 1)
                        ->searchable()
                        ->required(),
                    TextInput::make('diameter')
                        ->label('Diameter (cm)')
                        ->placeholder('13 cm - 50 cm')
                        ->required()
                        ->numeric()
                        ->rule('between:13,50')
                        ->validationMessages([
                            'between' => 'Wijaya hanya menerima kayu dengan diameter antara 13 cm hingga 50 cm.',
                        ])
                        ->afterStateUpdated(function ($state) {
                            if ($state === null)
                                return;

                            if ($state < 13) {
                                Notification::make()
                                    ->title('Ukuran Kayu Terlalu Kecil')
                                    ->body('Wijaya Tidak Menerima Kayu Berukuran Kurang Dari 13 cm.')
                                    ->warning()
                                    ->send();
                            } elseif ($state > 50) {
                                Notification::make()
                                    ->title('Ukuran Kayu Terlalu Besar')
                                    ->body('Wijaya Tidak Menerima Kayu Berukuran Lebih Dari 50 cm.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                    TextInput::make('jumlah_batang')
                        ->label('Jumlah Batang')
                        ->required()
                        ->default(1)
                        ->numeric(),
                ]);
    }
}
