<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use App\Models\Pegawai;
use App\Models\DetailPegawai;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;



class DetailPegawaisRelationManager extends RelationManager
{
    protected static ?string $title = 'Pegawai';
    protected static string $relationship = 'detailPegawais';

    // Izinkan Relation Manager tampil di halaman View
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    // IZINKAN TOMBOL CREATE MUNCUL DI VIEW
    public static function showTableHeaderActionsInView(): bool
    {
        return true;
    }

    // Izinkan membuat data dari View
    public static function canCreateForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    // FUNGSI BARU UNTUK MEMUNCULKAN TOMBOL DI HALAMAN VIEW
    public function isReadOnly(): bool
    {
        return false;
    }

    public static function timeOptions(): array
    {
        return collect(CarbonPeriod::create('00:00', '1 hour', '23:00')->toArray())
            ->mapWithKeys(fn($time) => [
                $time->format('H:i') => $time->format('H.i'),
            ])
            ->toArray();
    }

    public function form(Schema $schema): Schema
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

                Select::make('tugas')
                    ->label('Tugas')
                    ->options([
                        'operator' => 'Operator',
                        'asistenoperator' => 'Asisten Operator',
                        'dll' => 'Dll',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable(),

                Select::make('masuk')
                    ->label('Jam Masuk')
                    ->options(self::timeOptions())
                    ->default('06:00') // Default: 06:00 (sore)
                    ->required()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                    ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null), // Tampilkan hanya HH:MM,
                Select::make('pulang')
                    ->label('Jam Pulang')
                    ->options(self::timeOptions())
                    ->default('17:00') // Default: 17:00 (sore)
                    ->required()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state ? $state . ':00' : null)
                    ->formatStateUsing(fn($state) => $state ? substr($state, 0, 5) : null),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = DetailPegawai::where('id_produksi', $this->ownerRecord->id)
            ->where('id_pegawai', $data['id_pegawai'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'id_pegawai' => 'Pegawai ini sudah tercatat pada produksi yang sama.',
            ]);
        }

        return $data;
    }

    
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai') // Asumsi: relasi 'pegawai' & kolom 'nama'
                    ->label('Pegawai')
                    ->searchable(),

                TextColumn::make('tugas')
                    ->searchable(),

                TextColumn::make('masuk')
                    ->time('H:i'), // Format waktu agar rapi

                TextColumn::make('pulang')
                    ->time('H:i'), // Format waktu agar rapi

                TextColumn::make('ijin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ket')
                    ->label('Keterangan')
                    ->limit(50) // Batasi teks agar tidak terlalu panjang
                    ->tooltip(fn($record) => $record->ket) // Tampilkan teks penuh saat di-hover
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tempat filter jika Anda membutuhkannya
            ])
            ->headerActions([
                // INI ADALAH TOMBOL UNTUK MEMBUAT DATA BARU
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),

                // ➕ Tambah / Edit Ijin & Keterangan
                Action::make('aturIjin')
                    ->label(fn($record) => $record->ijin ? 'Edit Ijin' : 'Tambah Ijin')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        TextInput::make('ijin')->label('Ijin'),
                        Textarea::make('ket')->label('Keterangan'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'ijin' => $data['ijin'],
                            'ket'  => $data['ket'],
                        ]);
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
