<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Consultant';

    protected function getData(): array
    {
        $day = [];
        $TicketStats = Ticket::getStat();
        for ($i = 1; $i < 30; $i++) {
            $day[] = $i;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Ticket Consultant',
                    'data' => $TicketStats['chart'],
                    'backgroundColor' => '#4ade80',
                    'borderColor' => '#4ade80',
                ],
            ],
            'labels' => $day,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
