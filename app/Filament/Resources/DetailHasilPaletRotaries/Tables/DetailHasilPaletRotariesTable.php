<?php

namespace App\Filament\Resources\DetailHasilPaletRotaries\Tables;

use App\Models\DetailHasilPaletRotary;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DetailHasilPaletRotariesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            /*
            |--------------------------------------------------------------------------
            |  COLUMNS
            |--------------------------------------------------------------------------
            */
            ->columns([

                TextColumn::make('timestamp_laporan')
                    ->label('Waktu Laporan')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('lahan_display')
                    ->label('Lahan')
                    ->getStateUsing(
                        fn($record) =>
                        $record->penggunaanLahan?->lahan
                        ? "{$record->penggunaanLahan->lahan->kode_lahan} - {$record->penggunaanLahan->lahan->nama_lahan}"
                        : '-'
                    ),

                TextColumn::make('setoranPaletUkuran.dimensi')
                    ->label('Ukuran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kw')
                    ->label('KW')
                    ->searchable(),

                TextColumn::make('palet')
                    ->searchable(),

                TextColumn::make('total_lembar')
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

            /*
            |--------------------------------------------------------------------------
            |  QUERY SUBTOTAL JOIN (SAFE STRING ➜ INT)
            |--------------------------------------------------------------------------
            */
            ->modifyQueryUsing(function (Builder $query) {

                // Subtotal per Lahan — alias kolom id jadi lahan_id agar tidak bentrok
                $lahanSub = DB::table('detail_hasil_palet_rotaries')
                    ->selectRaw('id_penggunaan_lahan AS lahan_id,
                     SUM(CAST(total_lembar AS UNSIGNED)) AS total_lahan')
                    ->groupBy('id_penggunaan_lahan');

                // Subtotal per Ukuran — alias id_ukuran -> ukuran_id
                $ukuranSub = DB::table('detail_hasil_palet_rotaries')
                    ->selectRaw('id_ukuran AS ukuran_id,
                     SUM(CAST(total_lembar AS UNSIGNED)) AS total_ukuran')
                    ->groupBy('id_ukuran');

                // Subtotal per KW — alias kw -> kw_val (atau kw_key)
                $kwSub = DB::table('detail_hasil_palet_rotaries')
                    ->selectRaw('kw AS kw_val,
                     SUM(CAST(total_lembar AS UNSIGNED)) AS total_kw')
                    ->groupBy('kw');

                $query
                    ->leftJoinSub(
                        $lahanSub,
                        'subtotal_lahan',
                        'subtotal_lahan.lahan_id',
                        '=',
                        'detail_hasil_palet_rotaries.id_penggunaan_lahan'
                    )
                    ->leftJoinSub(
                        $ukuranSub,
                        'subtotal_ukuran',
                        'subtotal_ukuran.ukuran_id',
                        '=',
                        'detail_hasil_palet_rotaries.id_ukuran'
                    )
                    ->leftJoinSub(
                        $kwSub,
                        'subtotal_kw',
                        'subtotal_kw.kw_val',
                        '=',
                        'detail_hasil_palet_rotaries.kw'
                    )
                    ->addSelect([
                        'detail_hasil_palet_rotaries.*',
                        'subtotal_lahan.total_lahan',
                        'subtotal_ukuran.total_ukuran',
                        'subtotal_kw.total_kw',
                    ]);
            })

            /*
            |--------------------------------------------------------------------------
            |  GROUPING + SUBTOTAL DISPLAY
            |--------------------------------------------------------------------------
            */
            ->groups([

                /*
                 * Group LAHAN
                 */
                Group::make('id_penggunaan_lahan')
                    ->label('Lahan')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->penggunaanLahan?->lahan
                        ? "{$record->penggunaanLahan->lahan->kode_lahan} - {$record->penggunaanLahan->lahan->nama_lahan}"
                        : '-'
                    )
                    ->getDescriptionUsing(
                        fn($record) =>
                        "Total: " . number_format((int) $record->total_lahan)
                    ),

                /*
                 * Group UKURAN
                 */
                Group::make('id_ukuran')
                    ->label('Ukuran')
                    ->getTitleFromRecordUsing(
                        fn($state, $record) =>

                        ($record->setoranPaletUkuran?->dimensi ?? '-') .
                        " — Total: " .
                        number_format((int) $record->total_ukuran)

                    ),

                /*
                 * Group KW
                 */
                Group::make('kw')
                    ->label('KW')
                    ->getTitleFromRecordUsing(
                        fn($state, $record) =>
                        ($record->kw ?? '-') .
                        ' — Total: ' .
                        number_format((int) ($record->total_kw ?? 0))
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            |  ACTIONS
            |--------------------------------------------------------------------------
            */
            ->headerActions([
                CreateAction::make(),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
