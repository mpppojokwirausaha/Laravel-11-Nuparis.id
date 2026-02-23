<?php

namespace App\Filament\Resources\TossResource\Pages;

use App\Filament\Resources\TossResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditToss extends EditRecord
{
    protected static string $resource = TossResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
