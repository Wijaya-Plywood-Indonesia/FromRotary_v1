<?php

namespace App\Filament\Resources\BahanRepairs;

use App\Filament\Resources\BahanRepairs\Pages\CreateBahanRepair;
use App\Filament\Resources\BahanRepairs\Pages\EditBahanRepair;
use App\Filament\Resources\BahanRepairs\Pages\ListBahanRepairs;
use App\Filament\Resources\BahanRepairs\Schemas\BahanRepairForm;
use App\Filament\Resources\BahanRepairs\Tables\BahanRepairsTable;
use App\Models\BahanRepair;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BahanRepairResource extends Resource
{
    protected static ?string $model = BahanRepair::class;

    public static function showRegisterNavigation(): bool
    {

        return false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Repair';


    public static function form(Schema $schema): Schema
    {
        return BahanRepairForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BahanRepairsTable::configure($table);
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
            'index' => ListBahanRepairs::route('/'),
            'create' => CreateBahanRepair::route('/create'),
            'edit' => EditBahanRepair::route('/{record}/edit'),
        ];
    }
}
