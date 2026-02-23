<?php

namespace App\Filament\Clusters\Letters\Resources\LetterCategoryResource\Pages;

use App\Filament\Clusters\Letters\Resources\LetterCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLetterCategory extends CreateRecord
{
    protected static string $resource = LetterCategoryResource::class;
}
