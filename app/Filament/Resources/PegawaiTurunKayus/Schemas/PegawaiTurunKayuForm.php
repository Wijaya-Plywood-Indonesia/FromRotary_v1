<?php

namespace App\Filament\Resources\PegawaiTurunKayus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Pegawai;

class PegawaiTurunKayuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_turun_kayu')
                    ->label('Tanggal Turun Kayu')
                    ->relationship('turunKayu', 'tanggal', fn($query) => $query->orderBy('tanggal', 'desc'))
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->tanggal->format('d F Y'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('id_pegawai')
                    ->label('Pekerja')
                    ->relationship('pegawai', 'nama_pegawai')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->kode_pegawai} - {$record->nama_pegawai}")
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Bisa pilih lebih dari satu pegawai'),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->required(),
                TimePicker::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->required(),
            ]);
    }
}
