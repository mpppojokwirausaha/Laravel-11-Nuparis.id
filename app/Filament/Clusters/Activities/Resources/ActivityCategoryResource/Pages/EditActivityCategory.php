<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityCategoryResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityCategory extends EditRecord
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ActivityCategoryResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
