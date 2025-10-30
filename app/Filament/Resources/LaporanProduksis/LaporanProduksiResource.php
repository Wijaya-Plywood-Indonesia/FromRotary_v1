<?php

namespace App\Filament\Resources\LaporanProduksis;
use App\Filament\Resources\LaporanProduksis\Schemas\LaporanProduksiForm;
use App\Filament\Resources\LaporanProduksis\Tables\LaporanProduksisTable;
use App\Filament\Resources\LaporanProduksis\Pages\LaporanProduksi as LaporanProduksiPage;
use App\Models\LaporanProduksi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LaporanProduksiResource extends Resource
{
    protected static ?string $model = LaporanProduksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'LaporanProduksi';

    public static function form(Schema $schema): Schema
    {
        return LaporanProduksiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanProduksisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => LaporanProduksiPage::route('/'),
        ];
    }
}
