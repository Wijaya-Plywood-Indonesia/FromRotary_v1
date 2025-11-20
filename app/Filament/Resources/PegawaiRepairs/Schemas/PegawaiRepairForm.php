<?php

namespace App\Filament\Resources\PegawaiRepairs\Schemas;

use Carbon\CarbonPeriod;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Pegawai;
use Filament\Forms\Components\Placeholder;

class PegawaiRepairForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_pegawai')
                    ->label('Pegawai')
                    ->options(
                        Pegawai::query()
                            ->get()
                            ->mapWithKeys(fn($pegawai) => [
                                $pegawai->id => "{$pegawai->kode_pegawai} - {$pegawai->nama_pegawai}",
                            ])
                    )
                    //   ->multiple() // bisa pilih banyak
                    ->searchable()
                    ->required(),

                Select::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->options(self::timeOptions())
                    ->default('06:00') // Default: 06:00 (sore)
                    ->required()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                    ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null),
                Select::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->options(self::timeOptions())
                    ->default('15:00') // Default: 15:00 (sore)
                    ->required()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                    ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null),
                TextInput::make('ijin')
                    ->label('Ijin')
                    ->maxLength(255),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(255),
                TextInput::make('nomor_meja')
                    ->label('Nomor Meja')
                    ->numeric(),
            ]);
    }
    public static function timeOptions(): array
    {
        return collect(CarbonPeriod::create('00:00', '1 hour', '23:00')->toArray())
            ->mapWithKeys(fn($time) => [
                $time->format('H:i') => $time->format('H.i'),
            ])
            ->toArray();
    }
}
