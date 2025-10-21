<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PegawaiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_pegawai'),
                TextEntry::make('nama_pegawai'),
                TextEntry::make('no_telepon_pegawai'),
                TextEntry::make('jenis_kelamin_pegawai')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Laki-Laki' : 'Perempuan'),
                TextEntry::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->formatStateUsing(function ($state, $record) {
                        $tanggalMasuk = Carbon::parse($state);
                        $sekarang = Carbon::now();

                        // Hitung lama bergabung
                        $lamaBergabung = $tanggalMasuk->diff($sekarang);
                        $durasi = sprintf(
                            '%d tahun %d bulan',
                            $lamaBergabung->y,
                            $lamaBergabung->m
                        );

                        return "{$tanggalMasuk->format('d M Y')} (bergabung {$durasi})";
                    }),
                ImageEntry::make('foto')
                    ->label('Foto Pegawai')
                    ->disk('public') // sesuai dengan FileUpload sebelumnya
                    ->square()
                    ->alignCenter(),

                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
