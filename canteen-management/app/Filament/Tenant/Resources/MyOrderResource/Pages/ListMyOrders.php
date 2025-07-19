<?php

namespace App\Filament\Tenant\Resources\MyOrderResource\Pages;

use App\Filament\Tenant\Resources\MyOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListMyOrders extends ListRecords
{
    protected static string $resource = MyOrderResource::class;
}