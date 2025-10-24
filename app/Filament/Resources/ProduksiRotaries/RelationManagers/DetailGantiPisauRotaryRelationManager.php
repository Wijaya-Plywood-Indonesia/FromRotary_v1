<?php

namespace App\Filament\Resources\ProduksiRotaries\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\GantiPisauRotaries\Tables\GantiPisauRotariesTable;
use App\Filament\Resources\GantiPisauRotaries\Schemas\GantiPisauRotaryForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

class DetailGantiPisauRotaryRelationManager extends RelationManager
{
    protected static ?string $title = 'Ganti Pisau';
    protected static string $relationship = 'detailGantiPisauRotary';

    public function form(Schema $schema): Schema
    {
        return GantiPisauRotaryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return GantiPisauRotariesTable::configure($table)
            ->headerActions([
                CreateAction::make(),

                Action::make('buat_otomatis')
                    ->label('Ganti Pisau!')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire) {
                        $parent = $livewire->getOwnerRecord();

                        $parent->detailGantiPisauRotary()->create([
                            'jam_mulai_ganti_pisau' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
                            'jam_selesai_ganti' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
                        ]);
                    })
                    ->successNotificationTitle('Pisau Sedang Diganti!'),
            ])
        ;
    }
}
