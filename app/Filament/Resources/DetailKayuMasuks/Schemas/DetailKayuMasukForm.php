<?php

namespace App\Filament\Resources\DetailKayuMasuks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class DetailKayuMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()
                ->columns([
                    'default' => 2, // 2 kolom di layar besar
                    'sm' => 2,     // 1 kolom di mobile
                    'md' => 2,     // 2 kolom di tablet
                ])
                ->schema([

                    TextInput::make('diameter')
                        ->label('Diameter (cm)')
                        ->placeholder('13 cm - 50 cm')
                        ->required()
                        ->numeric()
                        ->rule('between:13,50')
                        ->validationMessages([
                            'between' => 'Wijaya hanya menerima kayu dengan diameter antara 13 cm hingga 50 cm.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state === null) {
                                return;
                            }

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

                    Select::make('panjang')
                        ->label('Panjang')
                        ->options([
                            130 => '130 cm',
                            260 => '260 cm',
                        ])
                        ->required()
                        ->default(130)
                        ->native(false),

                    Select::make('grade')
                        ->label('Grade')
                        ->options([
                            1 => 'Grade A',
                            2 => 'Grade B',
                        ])
                        ->required()
                        ->default(1)
                        ->native(false)
                        ->searchable(),
                    TextInput::make('jumlah_batang')
                        ->label('Jumlah Batang')
                        ->required()
                        ->numeric(),
                ]),
        ]);
    }
}
