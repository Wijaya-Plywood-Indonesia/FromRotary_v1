<?php

namespace App\Filament\Resources\KayuMasuks\RelationManagers;

use App\Filament\Resources\DetailKayuMasuks\Schemas\DetailKayuMasukForm;
use App\Filament\Resources\DetailKayuMasuks\Tables\DetailKayuMasuksTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailMasukanKayuRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailMasukanKayu';
    protected static ?string $title = 'Detail Kayu Masuk';


    public function form(Schema $schema): Schema
    {
        return DetailKayuMasukForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailKayuMasuksTable::configure($table);
    }
}
