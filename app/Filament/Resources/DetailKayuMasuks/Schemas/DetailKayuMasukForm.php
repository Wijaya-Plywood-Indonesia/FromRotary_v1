<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

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
                                ->mapWithKeys(function ($lahan) {
                                    return [
                                        $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                                    ];
                                })
                        )
                        ->searchable()
                        ->required(),
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
                        ])
                        ->required()
                        ->default(130)
                        ->native(false),

                    Select::make('id_jenis_kayu')
                        ->label('Jenis Kayu')
                        ->options(
                            JenisKayu::query()
                                ->get()
                                ->mapWithKeys(fn($JenisKayu) => [
                                    $JenisKayu->id => "{$JenisKayu->kode_kayu} - {$JenisKayu->nama_kayu}",
                                ])
                        )
                        ->searchable()
                        ->placeholder('Pilih Jenis Kayu')
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
                        ->numeric(),
                ]);
    }
}
