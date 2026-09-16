<?php

namespace App\Filament\Resources\CertificateHalalResource\Pages;

use App\Filament\Resources\CertificateHalalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificateHalals extends ListRecords
{
    protected static string $resource = CertificateHalalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
