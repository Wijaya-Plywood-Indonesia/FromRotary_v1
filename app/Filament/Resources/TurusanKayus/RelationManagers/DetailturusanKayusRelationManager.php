<?php

namespace App\Filament\Resources\TurusanKayus\RelationManagers;

use App\Models\DetailTurusanKayu;
use App\Models\JenisKayu;
use App\Models\Lahan;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class DetailturusanKayusRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailturusanKayus';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        TextInput::make('nomer_urut')
                            ->label('Nomor')
                            ->numeric()
                            ->required()
                            ->default(function (callable $get, $livewire) {
                                $parentRecord = $livewire->ownerRecord;
                                if (!$parentRecord)
                                    return 1;

                                $lastNumber = DetailTurusanKayu::where('id_kayu_masuk', $parentRecord->id)
                                    ->max('nomer_urut');
                                return $lastNumber ? $lastNumber + 1 : 1;
                            })
                            ->rules(function ($get, $livewire, $record) {
                                $parentRecord = $livewire->ownerRecord;
                                if (!$parentRecord) {
                                    return [];
                                }

                                return [
                                    Rule::unique('detail_turusan_kayus', 'nomer_urut')
                                        ->where('id_kayu_masuk', $parentRecord->id)
                                        ->where('lahan_id', $get('lahan_id')) // ✅ tambahkan ini
                                        ->ignore($record?->id),
                                ];
                            })
                            ->validationMessages([
                                'unique' => 'Nomor ini sudah digunakan pada kayu masuk dan lahan yang sama.',
                            ]),

                        Select::make('lahan_id')
                            ->label('Lahan')
                            ->options(
                                Lahan::query()
                                    ->get()
                                    ->mapWithKeys(fn($lahan) => [
                                        $lahan->id => "{$lahan->kode_lahan} - {$lahan->nama_lahan}",
                                    ])
                            )
                            ->default(fn() => DetailTurusanKayu::latest('id')->value('lahan_id') ?? 1)
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
                            ->default(fn() => DetailTurusanKayu::latest('id')->value('jenis_kayu_id') ?? 1)
                            ->searchable()
                            ->required(),

                        Select::make('panjang')
                            ->label('Panjang')
                            ->options([
                                130 => '130 cm',
                                260 => '260 cm',
                            ])
                            ->required()
                            ->default(fn() => DetailTurusanKayu::latest('id')->value('panjang') ?? 130)
                            ->searchable()
                            ->native(false),

                        Select::make('grade')
                            ->label('Grade')
                            ->options([
                                1 => 'Grade A',
                                2 => 'Grade B',
                            ])
                            ->required()
                            ->default(fn() => DetailTurusanKayu::latest('id')->value('grade') ?? 1)
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
                                cookie()->queue('filament_local_storage_detail_kayu_masuk.grade', $state, 60 * 24 * 30);
                            }),
                        TextInput::make('diameter')
                            ->label('Diameter (cm)')
                            ->placeholder('13 cm - 50 cm')
                            ->required()
                            ->numeric()
                            ->rule('between:13,50')
                            ->validationMessages([
                                'between' => 'Wijaya hanya menerima kayu dengan diameter antara 13 cm hingga 50 cm.',
                            ])
                            ->afterStateUpdated(function ($state) {
                                if ($state === null)
                                    return;

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
                    ])
                // ->columns([
                //     'default' => 1,  // di HP: 1 kolom
                //     'md' => 3,       // di tablet:  kolom
                //     'xl' => 3,       // di layar besar: 3 kolom
                // ]),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomer_urut')
                    ->label('NO')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

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
                    ->label('D')
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
                    ->successNotification(null) // matikan notifikasi default
                    ->label('Tambah Kayu')
                    ->after(function ($record) {
                        $diameter = $record->diameter ?? '-';
                        $nomerUrut = $record->nomer_urut ?? '-';

                        Notification::make()
                            ->title("Batang D : {$diameter} cm | No {$nomerUrut} ditambahkan")
                            ->success()
                            ->send();
                    }),

            ])
            ->groups([
                Group::make('lahan.kode_lahan')
                    ->label('Lahan')
                    ->collapsible()
                    ->getTitleFromRecordUsing(function ($record, $records = null) {
                        $kode = $record->lahan?->kode_lahan ?? '-';
                        $nama = $record->lahan?->nama_lahan ?? '-';
                        $jenis_kayu = $record->jenisKayu?->nama_kayu ?? '-';

                        // Jika $records tersedia gunakan itu (lebih cepat & pakai accessor kubikasi)
                        if ($records instanceof \Illuminate\Support\Collection && $records->isNotEmpty()) {
                            $totalBatang = $records->count();
                            $totalKubikasi = $records->sum(fn($r) => (float) $r->kubikasi);
                        } else {
                            // Fallback: hitung via query berdasarkan lahan_id dan parent (id_kayu_masuk)
                            $parentId = $record->id_kayu_masuk ?? $record->kayu_masuk_id ?? null;

                            $query = DetailTurusanKayu::query()
                                ->when($parentId, fn($q) => $q->where('id_kayu_masuk', $parentId))
                                ->where('lahan_id', $record->lahan_id)
                                ->get();

                            $totalBatang = $query->count();
                            $totalKubikasi = $query->sum(fn($r) => (float) $r->kubikasi);
                        }

                        $kubikasiFormatted = number_format($totalKubikasi, 4, ',', '.');

                        return "{$kode} {$nama} {$jenis_kayu} - {$totalBatang} batang ({$kubikasiFormatted} m³)";
                    }),
            ])
            ->defaultGroup('lahan.kode_lahan')
            ->groupingSettingsHidden()
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
