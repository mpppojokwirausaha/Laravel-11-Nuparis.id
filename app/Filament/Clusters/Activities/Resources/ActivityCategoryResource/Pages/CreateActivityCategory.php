<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityCategoryResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActivityCategory extends CreateRecord
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return ActivityCategoryResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
