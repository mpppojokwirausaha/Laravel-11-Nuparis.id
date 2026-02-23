<?php

namespace App\Filament\Consultations\Clusters\Tickets\Resources\TicketResource\Pages;

use App\Filament\Consultations\Clusters\Tickets\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
