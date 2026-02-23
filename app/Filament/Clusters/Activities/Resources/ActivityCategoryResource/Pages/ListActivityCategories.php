<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityCategoryResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityCategories extends ListRecords
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
