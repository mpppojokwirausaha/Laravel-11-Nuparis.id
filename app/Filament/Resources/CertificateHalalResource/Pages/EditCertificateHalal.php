<?php

namespace App\Filament\Resources\CertificateHalalResource\Pages;

use App\Filament\Resources\CertificateHalalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateHalal extends EditRecord
{
    protected static string $resource = CertificateHalalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
