<?php

namespace App\Filament\Resources\ProduksiJoints;

use App\Filament\Resources\ProduksiJoints\Pages\CreateProduksiJoint;
use App\Filament\Resources\ProduksiJoints\Pages\EditProduksiJoint;
use App\Filament\Resources\ProduksiJoints\Pages\ListProduksiJoints;
use App\Filament\Resources\ProduksiJoints\Schemas\ProduksiJointForm;
use App\Filament\Resources\ProduksiJoints\Tables\ProduksiJointsTable;
use App\Models\ProduksiJoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduksiJointResource extends Resource
{
    protected static ?string $model = ProduksiJoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return ProduksiJointForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksiJointsTable::configure($table);
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
            'index' => ListProduksiJoints::route('/'),
            'create' => CreateProduksiJoint::route('/create'),
            'edit' => EditProduksiJoint::route('/{record}/edit'),
        ];
    }
}
