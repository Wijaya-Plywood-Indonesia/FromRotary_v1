<?php

namespace App\Filament\Resources\ProduksiSandings;

use App\Filament\Resources\ProduksiSandings\Pages\CreateProduksiSanding;
use App\Filament\Resources\ProduksiSandings\Pages\EditProduksiSanding;
use App\Filament\Resources\ProduksiSandings\Pages\ListProduksiSandings;
use App\Filament\Resources\ProduksiSandings\Pages\ViewProduksiSanding;
use App\Filament\Resources\ProduksiSandings\Schemas\ProduksiSandingForm;
use App\Filament\Resources\ProduksiSandings\Schemas\ProduksiSandingInfolist;
use App\Filament\Resources\ProduksiSandings\Tables\ProduksiSandingsTable;
use App\Models\ProduksiSanding;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduksiSandingResource extends Resource
{
    protected static ?string $model = ProduksiSanding::class;
    protected static ?string $modelLabel = 'Produksi Sanding';
    protected static ?string $pluralModelLabel = 'Produksi Sanding';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDivide;

    public static function form(Schema $schema): Schema
    {
        return ProduksiSandingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProduksiSandingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksiSandingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                //

            RelationManagers\ModalSandingRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduksiSandings::route('/'),
            'create' => CreateProduksiSanding::route('/create'),
            'view' => ViewProduksiSanding::route('/{record}'),
            'edit' => EditProduksiSanding::route('/{record}/edit'),
        ];
    }
}
