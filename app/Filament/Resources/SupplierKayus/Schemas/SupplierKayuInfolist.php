<?php

namespace App\Filament\Resources\SupplierKayus\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class SupplierKayuInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nama_supplier'),
                TextEntry::make('no_telepon'),
                TextEntry::make('nik'),

                TextEntry::make('upload_ktp')
                    ->label('File KTP')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Lihat File' : 'Kosong')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->url(fn($state) => $state ? asset('storage/' . $state) : null)
                    ->openUrlInNewTab(),

                TextEntry::make('jenis_kelamin')
                    ->default(fn($get) => $get('jenis_kelamin') ? 'Laki-laki' : 'Perempuan')
                ,

                TextEntry::make('jenis_bank'),
                TextEntry::make('no_rekening'),
                TextEntry::make('status_supplier')
                    ->default(fn($get) => $get('status_supplier') ? 'Tidak Aktif' : 'Aktif')
                ,
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
