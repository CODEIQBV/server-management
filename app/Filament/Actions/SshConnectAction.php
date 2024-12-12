<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Process;

class SshConnectAction extends Action
{
    public static function getDefaultName(): string
    {
        return 'sshConnect';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-command-line')
            ->label('SSH Connect')
            ->color('success')
            ->action(function ($record): void {
                $sshUrl = sprintf(
                    'ssh://%s@%s:%d',
                    $record->username ?? 'root',
                    $record->public_ip,
                    $record->ssh_port ?? 22
                );
                
                match (PHP_OS_FAMILY) {
                    'Darwin' => Process::run("open '$sshUrl'"),
                    'Windows' => Process::run("start $sshUrl"),
                    default => Process::run("xdg-open '$sshUrl'")
                };
            })
            ->requiresConfirmation()
            ->modalHeading('Connect via SSH')
            ->modalDescription('This will open your SSH client to connect to the server.')
            ->modalSubmitActionLabel('Connect');
    }
}
