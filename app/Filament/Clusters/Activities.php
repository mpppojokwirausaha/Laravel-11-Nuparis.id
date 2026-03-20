<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Activities extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationUrl(): string
    {
        return '/management/activities/activities';
    }
}
