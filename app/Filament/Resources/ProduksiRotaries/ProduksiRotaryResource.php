<?php

namespace App\Filament\Resources\ProduksiRotaries;

use App\Filament\Resources\ProduksiRotaries\Pages\CreateProduksiRotary;
use App\Filament\Resources\ProduksiRotaries\Pages\EditProduksiRotary;
use App\Filament\Resources\ProduksiRotaries\Pages\ListProduksiRotaries;
use App\Filament\Resources\ProduksiRotaries\Pages\ViewProduksiRotary;
use App\Filament\Resources\ProduksiRotaries\Schemas\ProduksiRotaryForm;
use App\Filament\Resources\ProduksiRotaries\Schemas\ProduksiRotaryInfolist;
use App\Filament\Resources\ProduksiRotaries\Tables\ProduksiRotariesTable;
use App\Models\ProduksiRotary;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProduksiRotaryResource extends Resource
{
    protected static ?string $model = ProduksiRotary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;
    protected static string|UnitEnum|null $navigationGroup = 'Rotary';
    protected static ?int $navigationSort = 1;
    //ngurutin
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest('created_at');
    }
    public static function form(Schema $schema): Schema
    {
        return ProduksiRotaryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProduksiRotaryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksiRotariesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                //
            RelationManagers\DetailPegawaiRotaryRelationManager::class,
            RelationManagers\DetailLahanRotaryRelationManager::class,
            RelationManagers\DetailValidasiHasilRotaryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduksiRotaries::route('/'),
            'create' => CreateProduksiRotary::route('/create'),
            'view' => ViewProduksiRotary::route('/{record}'),
            'edit' => EditProduksiRotary::route('/{record}/edit'),
        ];
    }
}
