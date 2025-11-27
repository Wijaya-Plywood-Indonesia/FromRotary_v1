<?php

namespace App\Filament\Resources\RencanaTargets;

use App\Filament\Resources\RencanaTargets\Pages\CreateRencanaTarget;
use App\Filament\Resources\RencanaTargets\Pages\EditRencanaTarget;
use App\Filament\Resources\RencanaTargets\Pages\ListRencanaTargets;
use App\Filament\Resources\RencanaTargets\Schemas\RencanaTargetForm;
use App\Filament\Resources\RencanaTargets\Tables\RencanaTargetsTable;
use App\Models\RencanaTarget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RencanaTargetResource extends Resource
{
    protected static ?string $model = RencanaTarget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }


    public static function form(Schema $schema): Schema
    {
        return RencanaTargetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RencanaTargetsTable::configure($table);
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
            'index' => ListRencanaTargets::route('/'),
            'create' => CreateRencanaTarget::route('/create'),
            'edit' => EditRencanaTarget::route('/{record}/edit'),
        ];
    }
}
