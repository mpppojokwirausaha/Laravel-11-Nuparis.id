<?php

namespace App\Filament\Clusters\Events\Resources\OrderEventResource\Pages;

use App\Filament\Clusters\Events\Resources\OrderEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderEvent extends EditRecord
{
    protected static string $resource = OrderEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
