<?php

namespace App\Filament\Resources\ProduksiKedis\RelationManagers;

use App\Filament\Resources\DetailBongkarKedis\DetailBongkarKediResource;
use App\Filament\Resources\DetailBongkarKedis\Schemas\DetailBongkarKediForm;
use App\Filament\Resources\DetailBongkarKedis\Tables\DetailBongkarKedisTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DetailBongkarRelationManager extends RelationManager
{
    protected static ?string $title = 'Bongkar Kedi';
    protected static string $relationship = 'detailBongkarKedi';

    public function form(Schema $schema): Schema
    {
        return DetailBongkarKediForm::configure($schema);
    }
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return DetailBongkarKedisTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ])
        ;
    }
    public static function canViewForRecord($ownerRecord, $pageClass): bool
    {
        return $ownerRecord->status === 'bongkar';
    }
}
