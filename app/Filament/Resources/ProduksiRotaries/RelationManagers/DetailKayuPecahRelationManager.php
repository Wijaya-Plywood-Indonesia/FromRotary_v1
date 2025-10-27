<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\KayuPecahRotaries\Tables\KayuPecahRotariesTable;
use App\Filament\Resources\KayuPecahRotaries\Schemas\KayuPecahRotaryForm;

class DetailKayuPecahRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailKayuPecah';
    protected static ?string $title = 'Kayu Pecah';

    public function form(Schema $schema): Schema
    {
        return KayuPecahRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return KayuPecahRotariesTable::configure($table);
    }
}
