<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SshConnectAction;
use App\Filament\Resources\ServerResource\Pages;
use App\Filament\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use App\Services\EncryptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Infrastructure';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Core server details')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Production Web Server 1')
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('hostname')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., web1.example.com')
                                ->columnSpan(1),
                        ]),
                    ])
                    ->icon('heroicon-o-server'),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Section::make('Network Configuration')
                        ->description('Network connectivity details')
                        ->schema([
                            Forms\Components\TextInput::make('public_ip')
                                ->label('Public IP')
                                ->maxLength(255)
                                ->placeholder('e.g., 203.0.113.1')
                                ->suffixIcon('heroicon-o-globe-americas'),
                            Forms\Components\TextInput::make('internal_ip')
                                ->label('Internal IP')
                                ->maxLength(255)
                                ->placeholder('e.g., 10.0.0.10')
                                ->suffixIcon('heroicon-o-server-stack'),
                            Forms\Components\TextInput::make('ssh_port')
                                ->label('SSH Port')
                                ->default(22)
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(65535)
                                ->suffixIcon('heroicon-o-command-line'),
                        ])
                        ->icon('heroicon-o-globe-alt')
                        ->columnSpan(1),

                    Forms\Components\Section::make('Authentication')
                        ->description('Server access credentials')
                        ->schema([
                            Forms\Components\TextInput::make('username')
                                ->default('root')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('auth_type')
                                ->options([
                                    'password' => 'Password',
                                    'ssh_key' => 'SSH Key',
                                ])
                                ->default('ssh_key')
                                ->required()
                                ->live(),
                            Forms\Components\TextInput::make('encrypted_password')
                                ->password()
                                ->visible(fn (Forms\Get $get) => $get('auth_type') === 'password')
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? app(EncryptionService::class)->encrypt($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->formatStateUsing(function ($state) {
                                    if ($state) {
                                        return app(EncryptionService::class)->decrypt($state);
                                    }
                                    return null;
                                })
                                ->revealable(),
                            Forms\Components\Textarea::make('ssh_key')
                                ->visible(fn (Forms\Get $get) => $get('auth_type') === 'ssh_key')
                                ->columnSpanFull(),
                        ])
                        ->icon('heroicon-o-key')
                        ->columnSpan(1),
                ]),

                Forms\Components\Section::make('Server Resources')
                    ->description('Hardware specifications')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Card::make()->schema([
                                Forms\Components\TextInput::make('cpu_cores')
                                    ->label('CPU Cores')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffixIcon('heroicon-o-cpu-chip'),
                                Forms\Components\TextInput::make('cpu_threads')
                                    ->label('CPU Threads')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffixIcon('heroicon-o-cpu-chip'),
                            ])->columns(2),
                            Forms\Components\Card::make()->schema([
                                Forms\Components\TextInput::make('ram_gb')
                                    ->label('RAM (GB)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffixIcon('heroicon-o-square-3-stack-3d'),
                                Forms\Components\TextInput::make('disk_gb')
                                    ->label('Disk (GB)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->suffixIcon('heroicon-o-circle-stack'),
                            ])->columns(2),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('disk_type')
                                ->label('Primary Disk Type')
                                ->options([
                                    'ssd' => 'SSD',
                                    'nvme' => 'NVMe',
                                    'hdd' => 'HDD',
                                ])
                                ->suffixIcon('heroicon-o-circle-stack'),
                            Forms\Components\KeyValue::make('additional_disks')
                                ->label('Additional Disks')
                                ->addButtonLabel('Add Disk')
                                ->keyLabel('Mount Point')
                                ->valueLabel('Size (GB)')
                                ->reorderable(),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->icon('heroicon-o-cpu-chip'),

                Forms\Components\Section::make('Infrastructure')
                    ->description('Datacenter and network configuration')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('datacenter_id')
                                ->relationship('datacenter', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->label('Datacenter Name'),
                                    Forms\Components\TextInput::make('location')
                                        ->required()
                                        ->label('Location'),
                                ]),
                            Forms\Components\Select::make('network_id')
                                ->relationship('network', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('parent_server_id')
                                ->label('Parent Server (if VPS)')
                                ->relationship('parentServer', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->icon('heroicon-o-building-office-2'),

                Forms\Components\Section::make('Status & Notes')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'maintenance' => 'Maintenance',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->columnSpanFull()
                                ->placeholder('Add any additional notes about this server...'),
                        ]),
                    ])
                    ->icon('heroicon-o-document-text'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Server $record): string => $record->hostname),
                Tables\Columns\TextColumn::make('public_ip')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Public IP'),
                Tables\Columns\TextColumn::make('internal_ip')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Internal IP'),
                Tables\Columns\TextColumn::make('datacenter.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('network.name')
                    ->sortable()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('datacenter')
                    ->relationship('datacenter', 'name'),
                Tables\Filters\SelectFilter::make('network')
                    ->relationship('network', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                SshConnectAction::make('sshConnect'),
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
            RelationManagers\ChildServersRelationManager::class,
            RelationManagers\CredentialsRelationManager::class,
            RelationManagers\DatabasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }
}
