<?php

namespace App\Filament\Clusters\Letters\Resources\OrderLetterResource\Pages;

use App\Filament\Clusters\Letters\Resources\OrderLetterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderLetters extends ListRecords
{
    protected static string $resource = OrderLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
