<?php

namespace App\Filament\Resources\ServerResource\RelationManagers;

use App\Services\EncryptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Js;

class CredentialsRelationManager extends RelationManager
{
    protected static string $relationship = 'credentials';
    protected static ?string $title = 'Credentials';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., MySQL Root User'),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'ssh' => 'SSH',
                        'mysql' => 'MySQL',
                        'postgresql' => 'PostgreSQL',
                        'ftp' => 'FTP',
                        'sftp' => 'SFTP',
                        'other' => 'Other',
                    ]),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('encrypted_password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => app(EncryptionService::class)->encrypt($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->revealable()
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return app(EncryptionService::class)->decrypt($state);
                        }
                        return null;
                    }),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ssh' => 'danger',
                        'mysql' => 'info',
                        'postgresql' => 'warning',
                        'ftp' => 'success',
                        'sftp' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'ssh' => 'SSH',
                        'mysql' => 'MySQL',
                        'postgresql' => 'PostgreSQL',
                        'ftp' => 'FTP',
                        'sftp' => 'SFTP',
                        'other' => 'Other',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_password')
                    ->icon('heroicon-o-clipboard')
                    ->label('Copy Password')
                    ->color('gray')
                    ->after(function ($record) {
                        $decrypted = app(EncryptionService::class)->decrypt($record->encrypted_password);
                        Notification::make()
                            ->title('Password copied')
                            ->body($decrypted)
                            ->success()
                            ->send();
                    }),
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