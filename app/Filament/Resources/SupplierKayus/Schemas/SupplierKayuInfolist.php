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
                TextEntry::make('jenis_kelamin_label')
                    ->default(fn($get) => $get('jenis_kelamin_pegawai') ? 'Laki-laki' : 'Perempuan')
                ,

                TextEntry::make('jenis_bank'),
                TextEntry::make('no_rekening'),
                TextEntry::make('jenis_kelamin_label')
                    ->default(fn($get) => $get('status_supplier') ? 'Tidak Aktif' : 'Aktif')
                ,
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
