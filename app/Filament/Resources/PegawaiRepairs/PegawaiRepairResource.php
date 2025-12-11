<?php

namespace App\Filament\Resources\PegawaiRepairs;

use App\Filament\Resources\PegawaiRepairs\Pages\CreatePegawaiRepair;
use App\Filament\Resources\PegawaiRepairs\Pages\EditPegawaiRepair;
use App\Filament\Resources\PegawaiRepairs\Pages\ListPegawaiRepairs;
use App\Filament\Resources\PegawaiRepairs\Schemas\PegawaiRepairForm;
use App\Filament\Resources\PegawaiRepairs\Tables\PegawaiRepairsTable;
use App\Models\PegawaiRepair;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PegawaiRepairResource extends Resource
{
    protected static ?string $model = PegawaiRepair::class;


    public static function showRegisterNavigation(): bool
    {

        return false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Repair';
    public static function form(Schema $schema): Schema
    {
        return PegawaiRepairForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PegawaiRepairsTable::configure($table);
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
            'index' => ListPegawaiRepairs::route('/'),
            'create' => CreatePegawaiRepair::route('/create'),
            'edit' => EditPegawaiRepair::route('/{record}/edit'),
        ];
    }
}
