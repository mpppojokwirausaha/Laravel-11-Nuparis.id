<?php

namespace App\Filament\Clusters\Letters\Resources\LetterCategoryResource\Pages;

use App\Filament\Clusters\Letters\Resources\LetterCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLetterCategories extends ListRecords
{
    protected static string $resource = LetterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
