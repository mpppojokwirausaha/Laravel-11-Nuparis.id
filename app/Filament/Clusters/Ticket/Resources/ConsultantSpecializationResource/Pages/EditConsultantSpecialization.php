<?php

namespace App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource\Pages;

use App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultantSpecialization extends EditRecord
{
    protected static string $resource = ConsultantSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ConsultantSpecializationResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
