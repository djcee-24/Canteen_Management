<?php

namespace App\Filament\Tenant\Resources\MyMenuItemResource\Pages;

use App\Filament\Tenant\Resources\MyMenuItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyMenuItem extends CreateRecord
{
    protected static string $resource = MyMenuItemResource::class;
}