<?php

namespace App\Filament\Resources\TrustedHubResource\Pages;

use App\Filament\Resources\TrustedHubResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrustedHub extends EditRecord
{
    protected static string $resource = TrustedHubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
