<?php

namespace App\Filament\Resources\DocumentTossResource\Pages;

use App\Filament\Resources\DocumentTossResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTosses extends ListRecords
{
    protected static string $resource = DocumentTossResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
