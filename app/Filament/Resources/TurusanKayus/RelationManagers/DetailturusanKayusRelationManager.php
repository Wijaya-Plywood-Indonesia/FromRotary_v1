<?php

namespace App\Filament\Resources\TurusanKayus\RelationManagers;

use App\Filament\Resources\DetailTurusanKayus\Schemas\DetailTurusanKayuForm;

use App\Filament\Resources\DetailTurusanKayus\Tables\DetailTurusanKayusTable;
use DetailTurusan;
use DetailTurusanKayus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailturusanKayusRelationManager extends RelationManager
{
    protected static string $relationship = 'DetailturusanKayus';

    public function form(Schema $schema): Schema
    {
        return DetailTurusanKayuForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DetailTurusanKayusTable::configure($table);
    }
}
