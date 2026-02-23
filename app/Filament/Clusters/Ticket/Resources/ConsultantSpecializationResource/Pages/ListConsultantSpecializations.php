<?php

namespace App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource\Pages;

use App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultantSpecializations extends ListRecords
{
    protected static string $resource = ConsultantSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
