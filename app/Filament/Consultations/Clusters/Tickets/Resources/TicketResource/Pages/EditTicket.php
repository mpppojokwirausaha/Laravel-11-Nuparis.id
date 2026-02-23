<?php

namespace App\Filament\Consultations\Clusters\Tickets\Resources\TicketResource\Pages;

use App\Filament\Consultations\Clusters\Tickets\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
