<?php

namespace App\Filament\Resources\PegawaiDryerForm\Schemas;

use Filament\Forms;

class PegawaiDryerForm
{
    public static function make(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('id_pegawai')
                ->label('Pegawai')
                ->relationship('pegawai', 'nama') // pastikan relasi ada di model
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('tugas')
                ->label('Tugas')
                ->required()
                ->maxLength(255),

            Forms\Components\DateTimePicker::make('masuk')
                ->label('Waktu Masuk')
                ->seconds(false),

            Forms\Components\DateTimePicker::make('pulang')
                ->label('Waktu Pulang')
                ->seconds(false),

            Forms\Components\TextInput::make('ijin')
                ->label('Ijin')
                ->maxLength(255),

            Forms\Components\Textarea::make('ket')
                ->label('Keterangan')
                ->rows(2),

            Forms\Components\Select::make('id_produksi_dryer')
                ->label('Produksi Dryer')
                ->relationship('produksiDryer', 'nama_produk') // sesuaikan nama field
                ->searchable()
                ->required(),
        ]);
    }
}
