<?php

namespace App\Filament\Resources\ProduksiPressDryers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Facades\Filament;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

class ValidasiPressDryersRelationManager extends RelationManager
{
    protected static string $relationship = 'validasiPressDryers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('role')
                    ->label('Role Login')
                    ->default(function () {
                        $user = Filament::auth()->user();

                        if (!$user) {
                            return 'Tidak diketahui';
                        }

                        // Ambil role pertama dari user (karena bisa punya lebih dari satu)
                        /** @var User&HasRoles $user */
                        return $user->getRoleNames()->first() ?? 'Tidak diketahui';
                    })
                    ->disabled()
                    ->dehydrated(true), // tetap ikut disimpan ke database
                Select::make('status')
                    ->label('Status Validasi')
                    ->options([
                        'divalidasi' => 'Divalidasi',
                        'disetujui' => 'Disetujui',
                        'ditangguhkan' => 'Ditangguhkan',
                        'ditolak' => 'Ditolak',
                    ])
                
                    ->required()
                    ->native(false)
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(), // 👈 ini yang munculkan tombol "Tambah"
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
