<?php

namespace App\Filament\Resources\ValidasiHasilDryers;

use App\Filament\Resources\ValidasiHasilDryers\Pages\CreateValidasiHasilDryer;
use App\Filament\Resources\ValidasiHasilDryers\Pages\EditValidasiHasilDryer;
use App\Filament\Resources\ValidasiHasilDryers\Pages\ListValidasiHasilDryers;
use App\Filament\Resources\ValidasiHasilDryers\Schemas\ValidasiHasilDryerForm;
use App\Filament\Resources\ValidasiHasilDryers\Tables\ValidasiHasilDryersTable;
use App\Models\ValidasiHasilDryer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ValidasiHasilDryerResource extends Resource
{
    protected static ?string $model = ValidasiHasilDryer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ValidasiHasilDryerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ValidasiHasilDryersTable::configure($table);
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
            'index' => ListValidasiHasilDryers::route('/'),
            'create' => CreateValidasiHasilDryer::route('/create'),
            'edit' => EditValidasiHasilDryer::route('/{record}/edit'),
        ];
    }
}
