<?php

namespace App\Filament\Resources\DatacenterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ServersRelationManager extends RelationManager
{
    protected static string $relationship = 'servers';
    protected static ?string $title = 'Servers';
    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->hostname),
                Tables\Columns\TextColumn::make('public_ip')
                    ->searchable()
                    ->copyable()
                    ->label('Public IP'),
                Tables\Columns\TextColumn::make('internal_ip')
                    ->searchable()
                    ->copyable()
                    ->label('Internal IP'),
                Tables\Columns\TextColumn::make('ssh_port')
                    ->label('SSH Port'),
                Tables\Columns\IconColumn::make('auth_type')
                    ->icon(fn (string $state): string => match ($state) {
                        'password' => 'heroicon-o-key',
                        'ssh_key' => 'heroicon-o-document-text',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'maintenance' => 'warning',
                        'inactive' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['datacenter_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 