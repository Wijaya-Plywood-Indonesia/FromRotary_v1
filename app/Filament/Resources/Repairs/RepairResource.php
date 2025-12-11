<?php

namespace App\Filament\Resources\Repairs;

use App\Filament\Resources\Repairs\Pages\CreateRepair;
use App\Filament\Resources\Repairs\Pages\EditRepair;
use App\Filament\Resources\Repairs\Pages\ListRepairs;
use App\Filament\Resources\Repairs\Pages\ViewRepair;
use App\Filament\Resources\Repairs\Schemas\RepairForm;
use App\Filament\Resources\Repairs\Schemas\RepairInfolist;
use App\Filament\Resources\Repairs\Tables\RepairsTable;
use App\Models\Repair;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Resources\Repairs\RelationManagers\PegawaiRepairRelationManager;
use App\Filament\Resources\Repairs\RelationManagers\BahanRepairsRelationManager;
use App\Filament\Resources\Repairs\RelationManagers\ValidasiRepairsRelationManager;

class RepairResource extends Resource
{
    protected static ?string $model = Repair::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;
    protected static string|UnitEnum|null $navigationGroup = 'Repair';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RepairForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RepairInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepairsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PegawaiRepairRelationManager::class,
            BahanRepairsRelationManager::class,
            ValidasiRepairsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepairs::route('/'),
            'create' => CreateRepair::route('/create'),
            'view' => ViewRepair::route('/{record}'),
            'edit' => EditRepair::route('/{record}/edit'),
        ];
    }
}
