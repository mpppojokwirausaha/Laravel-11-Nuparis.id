<?php

namespace App\Filament\Clusters\Letters\Resources\LetterResource\Pages;

use App\Filament\Clusters\Letters\Resources\LetterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLetter extends CreateRecord
{
    protected static string $resource = LetterResource::class;
}
