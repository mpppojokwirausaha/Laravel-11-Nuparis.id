<?php

namespace App\Filament\Clusters\Events\Resources\OrderEventResource\Pages;

use App\Filament\Clusters\Events\Resources\OrderEventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderEvent extends CreateRecord
{
    protected static string $resource = OrderEventResource::class;
}
