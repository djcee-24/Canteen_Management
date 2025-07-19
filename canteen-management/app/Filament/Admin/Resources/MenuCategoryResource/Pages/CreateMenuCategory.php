<?php

namespace App\Filament\Admin\Resources\MenuCategoryResource\Pages;

use App\Filament\Admin\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuCategory extends CreateRecord
{
    protected static string $resource = MenuCategoryResource::class;
}