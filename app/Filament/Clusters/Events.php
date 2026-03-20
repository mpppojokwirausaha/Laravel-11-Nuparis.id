<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Events extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationUrl(): string
    {
        return '/management/events/events';
    }
}
