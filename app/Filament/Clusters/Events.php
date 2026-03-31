<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Events extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Resources';

    public static function getNavigationUrl(): string
    {
        return '/management/events/events';
    }
}
