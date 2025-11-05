<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ProduksiRotary;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class LaporanProduksi extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.laporan-produksi';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produksi Rotary';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProduksiRotary::query()
                    ->with([
                        'mesin',
                        'detailLahanRotary.lahan',
                        'detailLahanRotary.jenisKayu',
                        'detailPaletRotary.setoranPaletUkuran',
                        'detailPaletRotary.penggunaanLahan',
                    ])
            )
            ->columns([

                Tables\Columns\TextColumn::make('lahan_digunakan')
                    ->label('Lahan')
                    ->getStateUsing(function ($record) {
                        return $record->detailLahanRotary
                            ->pluck('lahan.kode_lahan')
                            ->unique()
                            ->implode(', ');
                    })
                    ->wrap(),


                Tables\Columns\TextColumn::make('total_batang')
                    ->label('Total Batang')
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailLahanRotary->sum('jumlah_batang')
                    )
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('id_mesin')
                    ->label('ID Mesin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mesin.nama_mesin')
                    ->label('Nama Mesin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_produksi')
                    ->label('Tanggal Produksi')
                    ->date('d/m/Y')
                    ->sortable(),



                Tables\Columns\TextColumn::make('jenis_kayu_digunakan')
                    ->label('Jenis Kayu')
                    ->getStateUsing(function ($record) {
                        return $record->detailLahanRotary
                            ->pluck('jenisKayu.nama_kayu')
                            ->unique()
                            ->implode(', ');
                    })
                    ->wrap(),


                Tables\Columns\TextColumn::make('total_palet')
                    ->label('Total Palet')
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailPaletRotary->sum('palet')
                    )
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('total_lembar')
                    ->label('Total Lembar')
                    ->getStateUsing(
                        fn($record) =>
                        $record->detailPaletRotary->sum('total_lembar')
                    )
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('kendala')
                    ->label('Kendala')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->kendala;
                    })
                    ->default('-')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tgl_produksi')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['dari'],
                                fn($q, $date) =>
                                $q->whereDate('tgl_produksi', '>=', $date)
                            )
                            ->when(
                                $data['sampai'],
                                fn($q, $date) =>
                                $q->whereDate('tgl_produksi', '<=', $date)
                            );
                    }),

                Tables\Filters\SelectFilter::make('id_mesin')
                    ->label('Mesin')
                    ->relationship('mesin', 'nama_mesin'),

                Tables\Filters\Filter::make('lahan')
                    ->form([
                        \Filament\Forms\Components\Select::make('id_lahan')
                            ->label('Lahan')
                            ->relationship('detailLahanRotary.lahan', 'kode_lahan')
                            ->searchable()
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['id_lahan'] ?? false) {
                            $query->whereHas('detailLahanRotary', function ($q) use ($data) {
                                $q->where('id_lahan', $data['id_lahan']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('tgl_produksi', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}