<?php

namespace App\Filament\Resources\ProduksiDempuls;

use App\Filament\Resources\ProduksiDempuls\Pages\CreateProduksiDempul;
use App\Filament\Resources\ProduksiDempuls\Pages\EditProduksiDempul;
use App\Filament\Resources\ProduksiDempuls\Pages\ListProduksiDempuls;
use App\Filament\Resources\ProduksiDempuls\Pages\ViewProduksiDempul;
use App\Filament\Resources\ProduksiDempuls\Schemas\ProduksiDempulForm;
use App\Filament\Resources\ProduksiDempuls\Schemas\ProduksiDempulInfolist;
use App\Filament\Resources\ProduksiDempuls\Tables\ProduksiDempulsTable;
use App\Models\ProduksiDempul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Resources\ProduksiDempuls\RelationManagers\RencanaPegawaiDempulRelationManager;

class ProduksiDempulResource extends Resource
{
    protected static ?string $model = ProduksiDempul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProduksiDempulForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProduksiDempulInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksiDempulsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RencanaPegawaiDempulRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduksiDempuls::route('/'),
            'create' => CreateProduksiDempul::route('/create'),
            'view' => ViewProduksiDempul::route('/{record}'),
            'edit' => EditProduksiDempul::route('/{record}/edit'),
        ];
    }
}
