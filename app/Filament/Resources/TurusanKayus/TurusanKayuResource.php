<?php

namespace App\Filament\Resources\TurusanKayus;

use App\Filament\Resources\TurusanKayus\Pages\CreateTurusanKayu;
use App\Filament\Resources\TurusanKayus\Pages\EditTurusanKayu;
use App\Filament\Resources\TurusanKayus\Pages\ListTurusanKayus;
use App\Filament\Resources\TurusanKayus\Schemas\TurusanKayuForm;
use App\Filament\Resources\TurusanKayus\Tables\TurusanKayusTable;
use App\Models\KayuMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TurusanKayuResource extends Resource
{
    protected static ?string $model = KayuMasuk::class;

    protected static ?string $navigationLabel = 'Turusan Kayu';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TurusanKayuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TurusanKayusTable::configure($table);
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
            'index' => ListTurusanKayus::route('/'),
            'create' => CreateTurusanKayu::route('/create'),
            'edit' => EditTurusanKayu::route('/{record}/edit'),
        ];
    }
}
