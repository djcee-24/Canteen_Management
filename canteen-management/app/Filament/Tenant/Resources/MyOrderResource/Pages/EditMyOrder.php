<?php

namespace App\Filament\Tenant\Resources\MyOrderResource\Pages;

use App\Filament\Tenant\Resources\MyOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyOrder extends EditRecord
{
    protected static string $resource = MyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}