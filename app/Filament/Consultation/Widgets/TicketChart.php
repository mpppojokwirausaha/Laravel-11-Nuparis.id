<?php

namespace App\Filament\Consultation\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

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
                    'label' => 'Ticket Consultation',
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
