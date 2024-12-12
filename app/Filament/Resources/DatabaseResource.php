<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DatabaseResource\Pages;
use App\Filament\Resources\DatabaseResource\RelationManagers;
use App\Models\Database;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DatabaseResource extends Resource
{
    protected static ?string $model = Database::class;
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Infrastructure';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Database Information')
                    ->schema([
                        Forms\Components\Select::make('server_id')
                            ->relationship('server', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'mysql' => 'MySQL',
                                'postgresql' => 'PostgreSQL',
                                'mongodb' => 'MongoDB',
                                'redis' => 'Redis',
                                'other' => 'Other',
                            ]),
                        Forms\Components\TextInput::make('port')
                            ->numeric()
                            ->default(fn ($get) => match ($get('type')) {
                                'mysql' => 3306,
                                'postgresql' => 5432,
                                'mongodb' => 27017,
                                'redis' => 6379,
                                default => null,
                            }),
                        Forms\Components\TextInput::make('version')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('charset')
                            ->default('utf8mb4')
                            ->visible(fn ($get) => in_array($get('type'), ['mysql', 'postgresql'])),
                        Forms\Components\TextInput::make('collation')
                            ->default('utf8mb4_unicode_ci')
                            ->visible(fn ($get) => in_array($get('type'), ['mysql', 'postgresql'])),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
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
                        'mysql' => 'info',
                        'postgresql' => 'warning',
                        'mongodb' => 'success',
                        'redis' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('port')
                    ->sortable(),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('server')
                    ->relationship('server', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'mysql' => 'MySQL',
                        'postgresql' => 'PostgreSQL',
                        'mongodb' => 'MongoDB',
                        'redis' => 'Redis',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDatabases::route('/'),
            'create' => Pages\CreateDatabase::route('/create'),
            'edit' => Pages\EditDatabase::route('/{record}/edit'),
        ];
    }
} 