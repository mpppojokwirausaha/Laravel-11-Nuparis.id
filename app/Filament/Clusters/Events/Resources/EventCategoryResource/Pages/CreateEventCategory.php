<?php

namespace App\Filament\Clusters\Events\Resources\EventCategoryResource\Pages;

use App\Filament\Clusters\Events\Resources\EventCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEventCategory extends CreateRecord
{
    protected static string $resource = EventCategoryResource::class;
}
