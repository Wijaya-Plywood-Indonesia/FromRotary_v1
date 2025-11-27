<?php

namespace App\Filament\Resources\RencanaRepairs;

use App\Filament\Resources\RencanaRepairs\Pages\CreateRencanaRepair;
use App\Filament\Resources\RencanaRepairs\Pages\EditRencanaRepair;
use App\Filament\Resources\RencanaRepairs\Pages\ListRencanaRepairs;
use App\Filament\Resources\RencanaRepairs\Pages\ViewRencanaRepair;
use App\Filament\Resources\RencanaRepairs\Schemas\RencanaRepairForm;
use App\Filament\Resources\RencanaRepairs\Schemas\RencanaRepairInfolist;
use App\Filament\Resources\RencanaRepairs\Tables\RencanaRepairsTable;
use App\Models\RencanaRepair;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\RencanaRepairs\RelationManagers\RencanaPegawaiRelationManager;
use App\Filament\Resources\RencanaRepairs\RelationManagers\RencanaTargetRelationManager;

class RencanaRepairResource extends Resource
{
    protected static ?string $model = RencanaRepair::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Rencana Kerja Repair';


    public static function form(Schema $schema): Schema
    {
        return RencanaRepairForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RencanaRepairInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RencanaRepairsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RencanaPegawaiRelationManager::class,
            RencanaTargetRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRencanaRepairs::route('/'),
            'create' => CreateRencanaRepair::route('/create'),
            'view' => ViewRencanaRepair::route('/{record}'),
            'edit' => EditRencanaRepair::route('/{record}/edit'),
        ];
    }
}
