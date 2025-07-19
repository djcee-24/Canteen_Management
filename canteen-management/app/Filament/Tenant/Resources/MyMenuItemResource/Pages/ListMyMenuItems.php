<?php

namespace App\Filament\Tenant\Resources\MyMenuItemResource\Pages;

use App\Filament\Tenant\Resources\MyMenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyMenuItems extends ListRecords
{
    protected static string $resource = MyMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}