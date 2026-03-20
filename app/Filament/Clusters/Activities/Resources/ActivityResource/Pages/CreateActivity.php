<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
