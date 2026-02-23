<?php

namespace App\Filament\Resources\TossResource\Pages;

use App\Filament\Resources\TossResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTosses extends ListRecords
{
    protected static string $resource = TossResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
