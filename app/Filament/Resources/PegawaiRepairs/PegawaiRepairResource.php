<?php

namespace App\Filament\Resources\PegawaiRepairs;

use App\Filament\Resources\PegawaiRepairs\Pages\CreatePegawaiRepair;
use App\Filament\Resources\PegawaiRepairs\Pages\EditPegawaiRepair;
use App\Filament\Resources\PegawaiRepairs\Pages\ListPegawaiRepairs;
use App\Filament\Resources\PegawaiRepairs\Pages\ViewPegawaiRepair;
use App\Filament\Resources\PegawaiRepairs\Schemas\PegawaiRepairForm;
use App\Filament\Resources\PegawaiRepairs\Schemas\PegawaiRepairInfolist;
use App\Filament\Resources\PegawaiRepairs\Tables\PegawaiRepairsTable;
use App\Models\PegawaiRepair;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PegawaiRepairResource extends Resource
{
    protected static ?string $model = PegawaiRepair::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PegawaiRepairForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PegawaiRepairInfolist::configure($schema);
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
            'view' => ViewPegawaiRepair::route('/{record}'),
            'edit' => EditPegawaiRepair::route('/{record}/edit'),
        ];
    }
}
