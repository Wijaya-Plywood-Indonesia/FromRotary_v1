<?php

namespace App\Filament\Resources\ProduksiSandingJoints;

use App\Filament\Resources\ProduksiSandingJoints\Pages\CreateProduksiSandingJoint;
use App\Filament\Resources\ProduksiSandingJoints\Pages\EditProduksiSandingJoint;
use App\Filament\Resources\ProduksiSandingJoints\Pages\ListProduksiSandingJoints;
use App\Filament\Resources\ProduksiSandingJoints\Schemas\ProduksiSandingJointForm;
use App\Filament\Resources\ProduksiSandingJoints\Tables\ProduksiSandingJointsTable;
use App\Models\ProduksiSandingJoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduksiSandingJointResource extends Resource
{
    protected static ?string $model = ProduksiSandingJoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return ProduksiSandingJointForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksiSandingJointsTable::configure($table);
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
            'index' => ListProduksiSandingJoints::route('/'),
            'create' => CreateProduksiSandingJoint::route('/create'),
            'edit' => EditProduksiSandingJoint::route('/{record}/edit'),
        ];
    }
}
