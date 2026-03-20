<?php

namespace App\Filament\Resources\DocumentTossResource\Pages;

use App\Filament\Resources\DocumentTossResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentToss extends EditRecord
{
    protected static string $resource = DocumentTossResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
