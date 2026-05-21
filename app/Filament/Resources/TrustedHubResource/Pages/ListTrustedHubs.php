<?php

namespace App\Filament\Resources\TrustedHubResource\Pages;

use App\Filament\Resources\TrustedHubResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTrustedHubs extends ListRecords
{
    protected static string $resource = TrustedHubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('visit_website')
                ->label('Visit ')
                ->icon('heroicon-o-globe-alt')
                ->url(env('DOMAIN_TRUSTEDHUB'))
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }
}
