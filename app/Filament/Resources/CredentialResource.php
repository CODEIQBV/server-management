<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CredentialResource\Pages;
use App\Models\ServerCredential;
use App\Services\EncryptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CredentialResource extends Resource
{
    protected static ?string $model = ServerCredential::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Security';
    protected static ?string $modelLabel = 'Server Credential';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Credential Information')
                    ->schema([
                        Forms\Components\Select::make('server_id')
                            ->relationship('server', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('server.name')
                    ->searchable()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('server')
                    ->relationship('server', 'name'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCredentials::route('/'),
            'create' => Pages\CreateCredential::route('/create'),
            'edit' => Pages\EditCredential::route('/{record}/edit'),
        ];
    }
} 