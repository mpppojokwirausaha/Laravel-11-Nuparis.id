<?php

namespace App\Filament\Clusters\Ticket\Resources\TicketResource\Pages;

use App\Filament\Clusters\Ticket\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
