<?php

namespace App\Filament\Clusters\Letters\Resources\OrderLetterResource\Pages;

use App\Filament\Clusters\Letters\Resources\OrderLetterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderLetter extends CreateRecord
{
    protected static string $resource = OrderLetterResource::class;
}
