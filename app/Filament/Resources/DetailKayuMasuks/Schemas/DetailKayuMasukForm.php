<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

use App\Models\DetailKayuMasuk;
use App\Models\JenisKayu;
use App\Models\Lahan;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class DetailKayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)

            ->components([


                Select::make('lahan_id')
                    ->label('Lahan')
                    ->options(
                        Lahan::query()
                            ->get()
                            ->mapWithKeys(fn($lahan) => [
                                $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                            ])
                    )
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailKayuMasuk::latest('id')->value('lahan_id');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
                    ->searchable()
                    ->required(),



                Select::make('panjang')
                    ->label('Panjang')
                    ->options([
                        130 => '130 cm',
                        260 => '260 cm',
                        0 => 'Tidak Diketahui',
                    ])
                    ->required()
                    ->default(function () {
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

                Select::make('grade')
                    ->label('Grade')
                    ->options([
                        1 => 'Grade A',
                        2 => 'Grade B',
                    ])
                    ->required()
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailKayuMasuk::latest('id')->value('grade');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
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

                Select::make('jenis_kayu_id')
                    ->label('Jenis Kayu')
                    ->options(
                        JenisKayu::query()
                            ->get()
                            ->mapWithKeys(fn($jenis) => [
                                $jenis->id => "{$jenis->kode_kayu} - {$jenis->nama_kayu}",
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
