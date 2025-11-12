<?php

namespace App\Filament\Resources\TurusanKayus\RelationManagers;

use App\Filament\Resources\DetailTurusanKayus\Schemas\DetailTurusanKayuForm;

use App\Filament\Resources\DetailTurusanKayus\Tables\DetailTurusanKayusTable;
use App\Models\DetailTurusanKayu;
use App\Models\JenisKayu;
use App\Models\Lahan;
use DetailTurusan;
use DetailTurusanKayus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailturusanKayusRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailturusanKayus';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ...
                TextInput::make('nomer_urut')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->required()
                    // ->disabled()
                    ->default(function (callable $get, $livewire) {
                        // Ambil id_kayu_masuk dari parent relation
                        $parentRecord = $livewire->ownerRecord;

                        if (!$parentRecord) {
                            return 1;
                        }

                        // Cek nomor terakhir untuk id_kayu_masuk ini
                        $lastNumber = DetailTurusanKayu::where('id_kayu_masuk', $parentRecord->id)
                            ->max('nomer_urut');

                        return $lastNumber ? $lastNumber + 1 : 1;
                    }),
                Select::make('lahan_id')
                    ->label('Lahan')
                    ->options(
                        Lahan::query()
                            ->get()
                            ->mapWithKeys(fn($lahan) => [
                                $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                            ])
                    )
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailTurusanKayu::latest('id')->value('lahan_id');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
                    ->searchable()
                    ->required(),

                Select::make('jenis_kayu_id')
                    ->label('Jenis Kayu')
                    ->options(
                        JenisKayu::query()
                            ->get()
                            ->mapWithKeys(fn($JenisKayu) => [
                                $JenisKayu->id => "{$JenisKayu->kode_kayu} - {$JenisKayu->nama_kayu}",
                            ])
                    )
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailTurusanKayu::latest('id')->value('jenis_kayu_id');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
                    ->searchable()
                    ->required(),
                Select::make('panjang')
                    ->label('Panjang')
                    ->options([
                        130 => '130 cm',
                        260 => '260 cm',
                    ])
                    ->required()
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailTurusanKayu::latest('id')->value('panjang');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
                    ->searchable()
                    ->native(false),

                Select::make('grade')
                    ->label('Grade')
                    ->options([
                        1 => 'Grade A',
                        2 => 'Grade B',
                    ])
                    ->required()
                    ->default(function () {
                        // Ambil lahan terakhir yang pernah diinputkan di detail_turusan_kayus
                        $lastLahan = DetailTurusanKayu::latest('id')->value('grade');

                        // Jika tidak ada data sama sekali, gunakan id = 1
                        return $lastLahan ?? 1;
                    })
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
                TextInput::make('diameter')
                    ->required()
                    ->numeric(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomer_urut')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lahan_display')
                    ->label('Lahan')
                    ->getStateUsing(fn($record) => "{$record->lahan->kode_lahan}")
                    ->sortable(['lahan.kode_lahan'])
                    ->searchable(['lahan.kode_lahan']),

                TextColumn::make('keterangan_kayu')
                    ->label('Kayu')
                    ->getStateUsing(function ($record) {
                        $namaKayu = $record->jenisKayu?->nama_kayu ?? '-';
                        $panjang = $record->panjang ?? '-';
                        $grade = match ($record->grade) {
                            1 => 'A',
                            2 => 'B',
                            default => '-',
                        };

                        return "{$namaKayu} {$panjang} ({$grade})";
                    })
                    ->sortable(['jenisKayu.nama_kayu', 'panjang', 'grade']) // tetap bisa diurutkan
                    ->searchable(['jenisKayu.nama_kayu', 'panjang']) // bisa dicari juga
                    //  ->badge()
                    ->color(fn($record) => match ($record->grade) {
                        1 => 'success',
                        2 => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('diameter')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nomer_urut', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Detail')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('no_urut')
                            ->numeric()
                            ->required(),
                        TextInput::make('nama_barang')
                            ->required(),
                        TextInput::make('jumlah')
                            ->numeric()
                            ->required(),
                    ])
                    ->modalHeading('Tambah Data Detail')
                    ->modalWidth('lg')
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_at'] = now();
                        return $data;
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ]);

    }
}
