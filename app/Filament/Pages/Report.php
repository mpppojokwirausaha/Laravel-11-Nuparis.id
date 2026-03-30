<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class Report extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.report';
    protected static ?string $title = 'Report';
    protected static ?string $navigationLabel = 'Report';
    protected static ?string $slug = 'Report';

    public ?array $data = [];
    public array $debugInfo = [];
    public Collection $filteredTickets;
    public array $reportData = [];
    public bool $isLoading = false;

    public function mount(): void
    {
        $this->filteredTickets = collect();
        $this->reportData = [];

        $this->form->fill([
            'ticket_type'       => 'all',
            'selected_tickets'  => [],
            'date_range'        => 'custom',
            'start_date'        => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date'          => Carbon::now()->format('Y-m-d'),
            'include_documents' => true,
            'group_by'          => 'ticket',
            'status_filter'     => 'all',
        ]);

        $this->addDebugInfo('Page mounted', [
            'default_period' => $this->form->getState()['start_date'] . ' to ' . $this->form->getState()['end_date']
        ]);
    }

    protected function getViewData(): array
    {
        return [
            'reportData' => $this->reportData,
            'groupBy'    => $this->data['group_by'] ?? 'ticket',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Progress')
                    ->description('Pilih kriteria laporan progress yang diinginkan')
                    ->schema([
                        Select::make('ticket_type')
                            ->label('Jenis Tiket')
                            ->options([
                                'all'      => 'Semua Tiket',
                                'selected' => 'Tiket Tertentu',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->addDebugInfo('Ticket type changed', ['type' => $state]);
                                $this->filteredTickets = collect();
                                $this->reportData = [];
                            }),

                        Select::make('selected_tickets')
                            ->label('Pilih Tiket')
                            ->placeholder('Pilih satu atau lebih tiket')
                            ->options($this->getTicketOptions())
                            ->multiple()
                            ->searchable()
                            ->visible(fn($get) => $get('ticket_type') === 'selected')
                            ->afterStateUpdated(function ($state) {
                                $this->addDebugInfo('Selected tickets changed', [
                                    'count' => count($state ?? [])
                                ]);
                                $this->reportData = [];
                            }),

                        Select::make('date_range')
                            ->label('Periode Laporan')
                            ->options([
                                'today'      => 'Hari Ini',
                                'yesterday'  => 'Kemarin',
                                'this_week'  => 'Minggu Ini',
                                'last_week'  => 'Minggu Lalu',
                                'this_month' => 'Bulan Ini',
                                'last_month' => 'Bulan Lalu',
                                'this_year'  => 'Tahun Ini',
                                'last_year'  => 'Tahun Lalu',
                                'custom'     => 'Kustom',
                            ])
                            ->default('custom')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->updateDateRange($state);
                                $this->reportData = [];
                            }),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn($get) => $get('date_range') === 'custom')
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->reportData = [];
                            }),

                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn($get) => $get('date_range') === 'custom')
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->reportData = [];
                            }),

                        Toggle::make('include_documents')
                            ->label('Sertakan Data Progress')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->reportData = [];
                            }),

                        Select::make('group_by')
                            ->label('Kelompokkan Berdasarkan')
                            ->options([
                                'ticket' => 'Per Tiket',
                                'status' => 'Per Status',
                                'month'  => 'Per Bulan',
                            ])
                            ->default('ticket')
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->reportData = [];
                            }),

                        Select::make('status_filter')
                            ->label('Filter Status')
                            ->options([
                                'all'         => 'Semua Status',
                                'open'        => 'Open',
                                'in_progress' => 'In Progress',
                                'closed'      => 'Closed',
                                'cancelled'   => 'Cancelled',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->reportData = [];
                            }),

                        Actions::make([
                            Action::make('generateReport')
                                ->label('📊 Lihat Progress')
                                ->color('primary')
                                ->icon('heroicon-o-document-chart-bar')
                                ->action('generateReport')
                                ->requiresConfirmation()
                                ->modalHeading('Lihat Progress')
                                ->modalDescription('Apakah Anda yakin ingin membuat laporan berdasarkan filter yang dipilih?')
                                ->modalSubmitActionLabel('Ya, Tampilkan')
                                ->modalCancelActionLabel('Batal'),

                            Action::make('exportReport')
                                ->label('📥 Export Laporan')
                                ->color('success')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->action('exportReport')
                                ->visible(fn() => !empty($this->reportData)),

                            Action::make('resetFilters')
                                ->label('⟳ Reset Filter')
                                ->color('gray')
                                ->icon('heroicon-o-arrow-path')
                                ->action('resetFilters'),
                        ])->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $this->isLoading = true;

        try {
            $this->addDebugInfo('Generating report', [
                'filters' => $this->form->getState()
            ]);

            $tickets = $this->getFilteredTickets();

            if ($tickets->isEmpty()) {
                Notification::make()
                    ->title('Informasi')
                    ->body('Tidak ada data tiket yang sesuai dengan filter yang dipilih.')
                    ->warning()
                    ->send();
                return;
            }

            $this->reportData = $this->generateReportData($tickets);

            $this->addDebugInfo('Report generated', [
                'total_tickets'    => $tickets->count(),
                'report_data_keys' => array_keys($this->reportData)
            ]);

            Notification::make()
                ->title('Sukses')
                ->body("Laporan berhasil ditampilkan. Total tiket: {$tickets->count()}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error generating report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->addDebugInfo('Error generating report', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error')
                ->body('Gagal menampilkan laporan: ' . $e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    protected function getFilteredTickets(): Collection
    {
        $state = $this->form->getState();

        $query = Ticket::query();

        if ($state['ticket_type'] === 'selected' && !empty($state['selected_tickets'])) {
            $query->whereIn('uuid', $state['selected_tickets']);
        }

        $this->applyDateFilter($query, $state);

        if ($state['status_filter'] !== 'all') {
            $query->where('ticket_status_uuid', $state['status_filter']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->get();
    }

    protected function applyDateFilter($query, array $state): void
    {
        $startDate = Carbon::parse($state['start_date'] ?? now()->startOfMonth())->startOfDay();
        $endDate   = Carbon::parse($state['end_date']   ?? now())->endOfDay();

        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    protected function updateDateRange(string $range): void
    {
        if ($range === 'custom') {
            return;
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now();

        switch ($range) {
            case 'today':
                $startDate = Carbon::today();
                $endDate   = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate   = Carbon::yesterday();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate   = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate   = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate   = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate   = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate   = Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate   = Carbon::now()->subYear()->endOfYear();
                break;
        }

        $this->form->fill(array_merge($this->data, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ]));
    }

    protected function generateReportData(Collection $tickets): array
    {
        $state            = $this->form->getState();
        $groupBy          = $state['group_by'];
        $includeDocuments = $state['include_documents'];

        $report = [
            'summary' => [
                'total_tickets' => $tickets->count(),
                'date_range'    => $state['start_date'] . ' s/d ' . $state['end_date'],
                'generated_at'  => Carbon::now()->format('d/m/Y H:i:s'),
                'generated_by'  => auth()->user()?->name ?? 'System',
            ],
            'data' => [],
        ];

        if ($groupBy === 'ticket') {
            foreach ($tickets as $ticket) {
                $ticketData = [
                    'ticket_code'     => $ticket->ticket_code,
                    'ticket_title'    => $ticket->ticket_title,
                    'status'          => $ticket->ticket_status_uuid ?? 'N/A',
                    'created_at'      => $ticket->created_at->format('d/m/Y H:i'),
                    'documents_count' => 0,
                    'documents'       => [],
                ];

                if ($includeDocuments) {
                    $progresses = $ticket->ticket_progress ?? [];

                    if (is_string($progresses)) {
                        $progresses = json_decode($progresses, true) ?? [];
                    }

                    $ticketData['documents_count'] = count($progresses);
                    $ticketData['documents'] = array_map(function ($p) {
                        $files = $p['file'] ?? [];
                        return [
                            'name'        => is_array($files) ? implode(', ', array_map('basename', $files)) : basename($files),
                            'description' => strip_tags($p['progress'] ?? ''),
                            'created_at'  => $p['timestamp'] ?? null,
                            'action'      => null,
                        ];
                    }, $progresses);
                }

                $report['data'][] = $ticketData;
            }
        } elseif ($groupBy === 'status') {
            $grouped = $tickets->groupBy('ticket_status_uuid');
            foreach ($grouped as $status => $statusTickets) {
                $report['data'][$status] = [
                    'status'  => $status ?: 'N/A',
                    'count'   => $statusTickets->count(),
                    'tickets' => $statusTickets->map(function ($ticket) {
                        return [
                            'code'       => $ticket->ticket_code,
                            'title'      => $ticket->ticket_title,
                            'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                        ];
                    })->toArray(),
                ];
            }
        } elseif ($groupBy === 'month') {
            $grouped = $tickets->groupBy(function ($ticket) {
                return $ticket->created_at->format('F Y');
            });

            foreach ($grouped as $month => $monthTickets) {
                $report['data'][$month] = [
                    'month'   => $month,
                    'count'   => $monthTickets->count(),
                    'tickets' => $monthTickets->map(function ($ticket) {
                        return [
                            'code'       => $ticket->ticket_code,
                            'title'      => $ticket->ticket_title,
                            'created_at' => $ticket->created_at->format('d/m/Y'),
                        ];
                    })->toArray(),
                ];
            }
        }

        return $report;
    }

    public function resetFilters(): void
    {
        $this->form->fill([
            'ticket_type'       => 'all',
            'selected_tickets'  => [],
            'date_range'        => 'custom',
            'start_date'        => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date'          => Carbon::now()->format('Y-m-d'),
            'include_documents' => true,
            'group_by'          => 'ticket',
            'status_filter'     => 'all',
        ]);

        $this->reportData      = [];
        $this->filteredTickets = collect();

        $this->addDebugInfo('Filters reset', []);

        Notification::make()
            ->title('Filter Direset')
            ->body('Semua filter telah dikembalikan ke pengaturan awal.')
            ->info()
            ->send();
    }

    public function exportReport(): void
    {
        try {
            if (empty($this->reportData)) {
                Notification::make()
                    ->title('Informasi')
                    ->body('Tidak ada data untuk diexport. Tampilkan laporan terlebih dahulu.')
                    ->warning()
                    ->send();
                return;
            }

            $this->addDebugInfo('Export report', [
                'data_size' => count($this->reportData)
            ]);

            Notification::make()
                ->title('Informasi')
                ->body('Fitur export akan segera tersedia.')
                ->info()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error exporting report', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error')
                ->body('Gagal export laporan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getTicketOptions(): array
    {
        try {
            return Ticket::select('uuid', 'ticket_code', 'ticket_title')
                ->whereNotNull('uuid')
                ->whereNotNull('ticket_code')
                ->orderBy('ticket_code')
                ->get()
                ->mapWithKeys(function ($ticket) {
                    return [$ticket->uuid => $ticket->ticket_code . ' - ' . $ticket->ticket_title];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading ticket options', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    #[Computed]
    public function getSummaryStats(): array
    {
        if (empty($this->reportData)) {
            return [];
        }

        $stats = [
            'total_tickets'   => $this->reportData['summary']['total_tickets'] ?? 0,
            'total_documents' => 0,
        ];

        if (isset($this->reportData['data']) && is_array($this->reportData['data'])) {
            foreach ($this->reportData['data'] as $item) {
                if (isset($item['documents_count'])) {
                    $stats['total_documents'] += $item['documents_count'];
                } elseif (isset($item['tickets']) && is_array($item['tickets'])) {
                    foreach ($item['tickets'] as $ticket) {
                        if (isset($ticket['documents_count'])) {
                            $stats['total_documents'] += $ticket['documents_count'];
                        }
                    }
                }
            }
        }

        return $stats;
    }

    #[Computed]
    public function hasReportData(): bool
    {
        return !empty($this->reportData);
    }

    #[Computed]
    public function getActiveFiltersCount(): int
    {
        $state = $this->form->getState();
        $count = 0;

        if ($state['ticket_type'] === 'selected' && !empty($state['selected_tickets'])) {
            $count++;
        }

        if ($state['date_range'] !== 'custom') {
            $count++;
        }

        if ($state['status_filter'] !== 'all') {
            $count++;
        }

        if ($state['group_by'] !== 'ticket') {
            $count++;
        }

        return $count;
    }

    private function addDebugInfo(string $action, array $data): void
    {
        if (config('app.debug')) {
            $this->debugInfo[] = [
                'time'   => now()->format('H:i:s.u'),
                'action' => $action,
                'data'   => $data,
            ];

            if (count($this->debugInfo) > 20) {
                $this->debugInfo = array_slice($this->debugInfo, -20);
            }
        }
    }
}
