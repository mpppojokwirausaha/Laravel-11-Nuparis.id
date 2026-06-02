<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class TicketChart extends Widget
{
    protected static ?string $pollingInterval = '10s';
    protected static bool $isLazy = true;
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.ticket-chart';

    public string $selectedPeriod = 'bulan_ini';

    public function updatedSelectedPeriod(string $value): void
    {
        $this->selectedPeriod = $value;
    }

    const STATUS_PENDING = '631266aa-dcd9-46ca-857b-43128d46edbd';
    const STATUS_OPEN    = '2ff4c17d-64ad-4db9-abd8-9e300cb1dbda';
    const STATUS_CLOSE   = '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c';

    /**
     * Get counts untuk periode tertentu
     */
    protected function getCountsByPeriod(?string $statusUuid = null, ?string $period = null): int
    {
        $period = $period ?? $this->selectedPeriod;

        $query = $statusUuid
            ? Ticket::where('ticket_status_uuid', $statusUuid)
            : Ticket::query();

        return match ($period) {
            'hari_ini'  => $query->whereDate('created_at', Carbon::today())->count(),
            'bulan_ini' => $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'tahun_ini' => $query->whereYear('created_at', now()->year)->count(),
            default     => 0,
        };
    }

    /**
     * Get counts untuk semua periode dalam satu array
     */
    protected function getAllCountsByPeriod(?string $statusUuid = null): array
    {
        return [
            'hari_ini'  => $this->getCountsByPeriod($statusUuid, 'hari_ini'),
            'bulan_ini' => $this->getCountsByPeriod($statusUuid, 'bulan_ini'),
            'tahun_ini' => $this->getCountsByPeriod($statusUuid, 'tahun_ini'),
        ];
    }

    /**
     * Get counts untuk periode sebelumnya (untuk perbandingan)
     */
    protected function getPreviousPeriodCounts(?string $statusUuid = null, ?string $period = null): int
    {
        $period = $period ?? $this->selectedPeriod;

        $query = $statusUuid
            ? Ticket::where('ticket_status_uuid', $statusUuid)
            : Ticket::query();

        return match ($period) {
            'hari_ini'  => $query->whereDate('created_at', Carbon::yesterday())->count(),
            'bulan_ini' => $query->whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count(),
            'tahun_ini' => $query->whereYear('created_at', now()->subYear()->year)->count(),
            default     => 0,
        };
    }

    /**
     * Get previous counts untuk semua periode
     */
    protected function getAllPreviousCountsByPeriod(?string $statusUuid = null): array
    {
        return [
            'hari_ini'  => $this->getPreviousPeriodCounts($statusUuid, 'hari_ini'),
            'bulan_ini' => $this->getPreviousPeriodCounts($statusUuid, 'bulan_ini'),
            'tahun_ini' => $this->getPreviousPeriodCounts($statusUuid, 'tahun_ini'),
        ];
    }

    // Method untuk navigasi ke halaman tickets
    public function goToTickets(?string $statusUuid = null, ?string $period = null): void
    {
        $period = $period ?? $this->selectedPeriod;

        $params = [];

        // Map period ke format yang benar
        $periodMap = [
            'hari_ini'  => 'today',
            'bulan_ini' => 'month',
            'tahun_ini' => 'year',
        ];

        // Filter period
        if ($period && isset($periodMap[$period])) {
            $params['tableFilters[created_at][period]'] = $periodMap[$period];
        }

        // Filter status - mapping UUID ke status name
        if ($statusUuid) {
            $statusName = $this->getStatusName($statusUuid);
            if ($statusName) {
                $params['tableFilters[kategori_status][status][0]'] = $statusName;
            }
        }

        // Redirect ke resource dengan filters
        $this->redirect(route('filament.management.ticket.resources.tickets.index', $params));
    }

    // Helper untuk mapping UUID ke status name
    protected function getStatusName(?string $statusUuid): ?string
    {
        return match ($statusUuid) {
            self::STATUS_PENDING => 'pending',
            self::STATUS_OPEN    => 'open',
            self::STATUS_CLOSE   => 'close',
            default => null,
        };
    }

    protected function getViewData(): array
    {
        // Hitung counts untuk masing-masing status (semua periode)
        $pendingCounts = $this->getAllCountsByPeriod(self::STATUS_PENDING);
        $openCounts = $this->getAllCountsByPeriod(self::STATUS_OPEN);
        $closedCounts = $this->getAllCountsByPeriod(self::STATUS_CLOSE);
        $totalCounts = $this->getAllCountsByPeriod();

        // Hitung previous counts untuk trend (semua periode)
        $pendingPrevCounts = $this->getAllPreviousCountsByPeriod(self::STATUS_PENDING);
        $openPrevCounts = $this->getAllPreviousCountsByPeriod(self::STATUS_OPEN);
        $closedPrevCounts = $this->getAllPreviousCountsByPeriod(self::STATUS_CLOSE);
        $totalPrevCounts = $this->getAllPreviousCountsByPeriod();

        $columns = [
            [
                'key'           => 'pending',
                'label'         => 'Pending',
                'status'        => self::STATUS_PENDING,
                'color'         => 'amber',
                'counts'        => $pendingCounts,
                'prev_counts'   => $pendingPrevCounts,
            ],
            [
                'key'           => 'open',
                'label'         => 'Open',
                'status'        => self::STATUS_OPEN,
                'color'         => 'teal',
                'counts'        => $openCounts,
                'prev_counts'   => $openPrevCounts,
            ],
            [
                'key'           => 'closed',
                'label'         => 'Close',
                'status'        => self::STATUS_CLOSE,
                'color'         => 'green',
                'counts'        => $closedCounts,
                'prev_counts'   => $closedPrevCounts,
            ],
            [
                'key'           => 'total',
                'label'         => 'Total',
                'status'        => null,
                'color'         => 'blue',
                'counts'        => $totalCounts,
                'prev_counts'   => $totalPrevCounts,
            ],
        ];

        return [
            'columns'           => $columns,
            'selectedPeriod'    => $this->selectedPeriod,
            'periodLabels'      => [
                'hari_ini'  => 'Hari ini',
                'bulan_ini' => 'Bulan ini',
                'tahun_ini' => 'Tahun ini',
            ],
        ];
    }
}
