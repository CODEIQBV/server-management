<?php

namespace App\Filament\Resources\DatabaseResource\RelationManagers;

use App\Services\EncryptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Database Users';
    protected static ?string $recordTitleAttribute = 'username';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('encrypted_password')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => app(EncryptionService::class)->encrypt($state))
                    ->revealable(),
                Forms\Components\Select::make('privileges')
                    ->multiple()
                    ->options([
                        'select' => 'SELECT',
                        'insert' => 'INSERT',
                        'update' => 'UPDATE',
                        'delete' => 'DELETE',
                        'create' => 'CREATE',
                        'drop' => 'DROP',
                        'reload' => 'RELOAD',
                        'shutdown' => 'SHUTDOWN',
                        'process' => 'PROCESS',
                        'file' => 'FILE',
                        'grant' => 'GRANT',
                        'references' => 'REFERENCES',
                        'index' => 'INDEX',
                        'alter' => 'ALTER',
                        'show_db' => 'SHOW DATABASES',
                        'super' => 'SUPER',
                        'create_tmp' => 'CREATE TEMPORARY TABLES',
                        'lock_tables' => 'LOCK TABLES',
                        'execute' => 'EXECUTE',
                        'repl_slave' => 'REPLICATION SLAVE',
                        'repl_client' => 'REPLICATION CLIENT',
                        'create_view' => 'CREATE VIEW',
                        'show_view' => 'SHOW VIEW',
                        'create_routine' => 'CREATE ROUTINE',
                        'alter_routine' => 'ALTER ROUTINE',
                        'create_user' => 'CREATE USER',
                        'event' => 'EVENT',
                        'trigger' => 'TRIGGER',
                    ])
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('privileges')
                    ->badge()
                    ->separator(',')
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_password')
                    ->icon('heroicon-o-clipboard')
                    ->label('Copy Password')
                    ->action(function ($record) {
                        return app(EncryptionService::class)->decrypt($record->encrypted_password);
                    })
                    ->successNotificationTitle('Password copied to clipboard'),
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