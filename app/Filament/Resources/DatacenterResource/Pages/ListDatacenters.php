<?php

namespace App\Filament\Resources\DatacenterResource\Pages;

use App\Filament\Resources\DatacenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDatacenters extends ListRecords
{
    protected static string $resource = DatacenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
