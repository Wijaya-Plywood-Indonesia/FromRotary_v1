<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('kode_pegawai')
                    ->required()
                    ->unique(
                        table: 'pegawais',
                        column: 'kode_pegawai',
                        ignoreRecord: true
                    )
                    ->validationMessages([
                        'unique' => 'Kode pegawai ini sudah digunakan. Silakan gunakan kode lain.',
                    ]),

                TextInput::make('nama_pegawai')
                    ->required(),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                TextInput::make('no_telepon_pegawai')
                    ->tel(),
                Select::make('jenis_kelamin_pegawai')
                    ->label('Jenis Kelamin')
                    ->options([
                        '0' => 'Perempuan',
                        '1' => 'Laki-laki',
                    ])
                    ->default('0') // default Perempuan
                    ->required(),
                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->required()
                // ->rule('before_or_equal:' . now()->subYears(17)->toDateString())
                // ->validationMessages([
                //     'before_or_equal' => 'Pegawai harus berusia minimal 17 tahun untuk mendaftar.',
                // ])
                ,
                FileUpload::make('foto')
                    ->label('Foto 3x4 atau 4x6')
                    ->image()
                    ->disk('public')
                    ->directory('pegawai')
                    ->maxSize(2048)
                    ->required()
                    ->imageEditor()
                    ->imageCropAspectRatio('3:4')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get) {
                        // Ambil nama pegawai dari input lain di form
                        $nama = $get('nama_pegawai') ?? 'pegawai';

                        // Bersihkan nama agar aman untuk nama file (tanpa spasi/simbol)
                        $nama_slug = Str::slug($nama);

                        // Kembalikan nama file yang rapi, misalnya "pegawai-budi-santoso.jpg"
                        return $nama_slug . '.' . $file->getClientOriginalExtension();
                    }),
            ]);
    }
}
