<?php

namespace App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource\Pages;

use App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateConsultantSpecialization extends CreateRecord
{
    protected static string $resource = ConsultantSpecializationResource::class;

    protected function getRedirectUrl(): string
    {
        return ConsultantSpecializationResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
