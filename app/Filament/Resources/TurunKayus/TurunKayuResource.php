<?php

namespace App\Filament\Resources\TurunKayus;

use App\Filament\Resources\TurunKayus\Pages\CreateTurunKayu;
use App\Filament\Resources\TurunKayus\Pages\EditTurunKayu;
use App\Filament\Resources\TurunKayus\Pages\ListTurunKayus;
use App\Filament\Resources\TurunKayus\Schemas\TurunKayuForm;
use App\Filament\Resources\TurunKayus\Tables\TurunKayusTable;
use App\Models\TurunKayu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TurunKayuResource extends Resource
{
    protected static ?string $model = TurunKayu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TurunKayuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TurunKayusTable::configure($table);
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
            'index' => ListTurunKayus::route('/'),
            'create' => CreateTurunKayu::route('/create'),
            'edit' => EditTurunKayu::route('/{record}/edit'),
        ];
    }
}
