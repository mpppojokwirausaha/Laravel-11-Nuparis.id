<?php

namespace App\Filament\Clusters\Events\Resources\EventResource\Pages;

use App\Filament\Clusters\Events\Resources\EventResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Event')
                ->color('warning')
                ->icon('heroicon-o-pencil-square'),
            Actions\Action::make('back')
                ->label('Kembali')
                ->url(EventResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
