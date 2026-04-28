<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Report extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.report';
    protected static ?string $title = 'Report';
    protected static ?string $navigationLabel = 'Report';
    protected static ?string $slug = 'Report';

    public ?array $data = [];
    public array $reportSummary = [];
    public bool $hasReport = false;
    public bool $isLoading = false;
    public array $debugInfo = [];

    private string $cacheKey;

    public function boot(): void
    {
        $this->cacheKey = 'report_data_' . auth()->id() . '_' . session()->getId();
    }

    public function mount(): void
    {
        $this->hasReport = false;
        $this->reportSummary = [];
        $this->debugInfo = [];
        $this->form->fill($this->defaultFormState());
    }

    protected function getViewData(): array
    {
        $reportData = $this->hasReport ? cache()->get($this->cacheKey, []) : [];

        return [
            'reportData' => $reportData,
            'reportSummary' => $this->reportSummary,
            'debugInfo' => $this->debugInfo,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Progress')
                    ->description('Pilih kriteria laporan progress yang diinginkan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('selected_tickets')
                                    ->label('Pilih Tiket')
                                    ->placeholder('Pilih tiket terlebih dahulu...')
                                    ->options(fn() => $this->getTicketOptions())
                                    ->searchable()
                                    ->live()
                                    ->columnSpan(fn($get) => !empty($get('selected_tickets')) ? 1 : 2)
                                    ->afterStateUpdated(function () {
                                        $this->clearReport();
                                    }),

                                Select::make('date_range')
                                    ->label('Periode Laporan')
                                    ->options([
                                        'today' => 'Hari Ini',
                                        'yesterday' => 'Kemarin',
                                        'this_week' => 'Minggu Ini',
                                        'last_week' => 'Minggu Lalu',
                                        'this_month' => 'Bulan Ini',
                                        'last_month' => 'Bulan Lalu',
                                        'this_year' => 'Tahun Ini',
                                        'last_year' => 'Tahun Lalu',
                                        'custom' => 'Kustom',
                                    ])
                                    ->default(null)
                                    ->live()
                                    ->hidden(fn($get) => empty($get('selected_tickets')))
                                    ->afterStateUpdated(function ($state, $get) {
                                        if ($state !== null) {
                                            $this->updateDateRange($state);
                                            $this->populateTicketFields($get('selected_tickets'));
                                        }
                                        $this->clearReport();
                                    }),
                            ])
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn($get) => !empty($get('selected_tickets')) && $get('date_range') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn() => $this->clearReport()),

                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn($get) => !empty($get('selected_tickets')) && $get('date_range') === 'custom')
                            ->live()
                            ->afterStateUpdated(fn() => $this->clearReport()),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('client_name')
                                    ->label('Nama Client')
                                    ->placeholder(function ($get) {
                                        $originalName = $this->getOriginalClientName($get('selected_tickets'));
                                        return !empty($originalName) ? $originalName : 'Masukkan nama client...';
                                    })
                                    ->helperText(function ($get) {
                                        $originalName = $this->getOriginalClientName($get('selected_tickets'));
                                        if (!empty($originalName)) {
                                            return 'Nama client dari database: ' . $originalName;
                                        }
                                        return '⚠️ Data client kosong, silakan isi manual';
                                    })
                                    ->default(function ($get) {
                                        return $this->getOriginalClientName($get('selected_tickets'));
                                    })
                                    ->columnSpan(1),

                                TextInput::make('proposal_for')
                                    ->label('Proposal For')
                                    ->placeholder('Otomatis terisi dari tiket...')
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->visible(fn($get) => !empty($get('selected_tickets')) && !empty($get('date_range'))),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('proposal_id')
                                    ->label('Proposal ID')
                                    ->placeholder('Masukkan Proposal ID...')
                                    ->columnSpan(1),

                                TextInput::make('enquiry')
                                    ->label('Enquiry')
                                    ->placeholder('Masukkan nomor/keterangan enquiry...')
                                    ->columnSpan(1),
                            ])
                            ->visible(fn($get) => !empty($get('selected_tickets')) && !empty($get('date_range'))),

                        TextInput::make('generated_by')
                            ->label('Dibuat oleh')
                            ->placeholder('Nama pembuat laporan...')
                            ->columnSpanFull()
                            ->visible(fn($get) => !empty($get('selected_tickets')) && !empty($get('date_range'))),

                        Actions::make([
                            Action::make('generateReport')
                                ->label('Lihat Progress')
                                ->color('primary')
                                ->icon('heroicon-o-document-chart-bar')
                                ->action('generateReport')
                                ->requiresConfirmation()
                                ->modalHeading('Lihat Progress')
                                ->modalDescription('Apakah Anda yakin ingin membuat laporan berdasarkan filter yang dipilih?')
                                ->modalSubmitActionLabel('Ya, Tampilkan')
                                ->modalCancelActionLabel('Batal')
                                ->visible(fn($get) => !empty($get('selected_tickets')) && !empty($get('date_range'))),

                            Action::make('exportReport')
                                ->label('Export PDF')
                                ->color('success')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->action('exportReport')
                                ->visible(fn() => $this->hasReport),

                            Action::make('resetFilters')
                                ->label('⟳ Reset Filter')
                                ->color('gray')
                                ->icon('heroicon-o-arrow-path')
                                ->action('resetFilters')
                                ->visible(fn($get) => !empty($get('selected_tickets'))),
                        ])->columnSpanFull(),
                    ])->columns(2),

                Section::make('Summary')
                    ->description('Tambahkan summary atau keterangan untuk laporan ini')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        RichEditor::make('admin_notes')
                            ->label('')
                            ->placeholder('Tulis summary di sini...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'link',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn() => $this->hasReport)
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->statePath('data');
    }

    protected function getOriginalClientName(?string $uuid): ?string
    {
        if (empty($uuid)) return null;

        $ticket = Ticket::where('uuid', $uuid)->first();
        return $ticket?->ticket_name_client ?? null;
    }

    protected function populateTicketFields(?string $uuid): void
    {
        if (empty($uuid)) {
            $this->form->fill(array_merge($this->data ?? [], [
                'client_name' => null,
                'proposal_for' => null,
            ]));
            return;
        }

        $ticket = Ticket::where('uuid', $uuid)->first();
        if (!$ticket) {
            return;
        }

        $this->form->fill(array_merge($this->data ?? [], [
            'client_name' => $ticket->ticket_name_client ?? '',
            'proposal_for' => $ticket->ticket_title ?? '-',
        ]));
    }

    public function generateReport(): void
    {
        $this->isLoading = true;

        try {
            $tickets = $this->getFilteredTickets();

            if ($tickets->isEmpty()) {
                Notification::make()
                    ->title('Informasi')
                    ->body('Tidak ada data tiket yang sesuai dengan filter yang dipilih.')
                    ->warning()
                    ->send();
                return;
            }

            $reportData = $this->buildReport($tickets);

            if (empty($reportData['data'])) {
                Notification::make()
                    ->title('Informasi')
                    ->body('Tidak ada progress tiket dalam periode yang dipilih.')
                    ->warning()
                    ->send();
                return;
            }

            cache()->put($this->cacheKey, $reportData, now()->addHours(2));

            $this->reportSummary = $reportData['summary'];
            $this->hasReport = true;

            Notification::make()
                ->title('Sukses')
                ->body("Laporan berhasil ditampilkan. Total tiket: {$tickets->count()}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error generating report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

        $query = Ticket::query()->orderBy('created_at', 'desc')->limit(1000);

        if (!empty($state['selected_tickets'])) {
            $query->where('uuid', $state['selected_tickets']);
        }

        $query->whereNotNull('ticket_progress')
            ->where('ticket_progress', '!=', '[]')
            ->where('ticket_progress', '!=', 'null');

        return $query->get();
    }

    protected function filterProgressesByDate(array $progresses, array $state): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($state);
        return array_values(array_filter($progresses, function ($p) use ($startDate, $endDate) {
            if (empty($p['timestamp'])) return false;
            return Carbon::parse($p['timestamp'])->between($startDate, $endDate);
        }));
    }

    protected function resolveDateRange(array $state): array
    {
        $range = $state['date_range'] ?? 'custom';

        if ($range !== 'custom') {
            [$start, $end] = match ($range) {
                'today' => [Carbon::today(), Carbon::today()],
                'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
                'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
                'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
                'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
                'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
                'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
                'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
                default => [Carbon::now()->startOfMonth(), Carbon::now()],
            };
            return [$start->startOfDay(), $end->endOfDay()];
        }

        return [
            Carbon::parse($state['start_date'] ?? now()->startOfMonth())->startOfDay(),
            Carbon::parse($state['end_date'] ?? now())->endOfDay(),
        ];
    }

    protected function updateDateRange(?string $range): void
    {
        if ($range === null || $range === 'custom') return;

        [$startDate, $endDate] = match ($range) {
            'today' => [Carbon::today(), Carbon::today()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };

        $this->form->fill(array_merge($this->data, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]));
    }

    protected function buildReport(Collection $tickets): array
    {
        $state = $this->form->getState();
        $data = $this->groupByTicket($tickets);

        // Get ticket code from first ticket data
        $ticketCode = '-';
        if (!empty($data) && isset($data[0]['ticket_code'])) {
            $ticketCode = $data[0]['ticket_code'];
        }

        // Get current timestamp for report generation
        $generatedByName = $state['generated_by'] ?? auth()->user()?->name ?? 'System';
        $generatedAt = Carbon::now();
        $generatedByWithTimestamp = $generatedByName . '; ' . $generatedAt->translatedFormat('d F Y H:i');

        return [
            'summary' => [
                'total_tickets' => count($data),
                'ticket_code' => $ticketCode,
                'date_range' => (function () use ($state) {
                    [$start, $end] = $this->resolveDateRange($state);
                    return $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y');
                })(),
                'generated_at' => $generatedAt->translatedFormat('d M Y H:i:s'),
                'generated_by' => $generatedByWithTimestamp,
                'generated_by_name' => $generatedByName,
                'generated_timestamp' => $generatedAt->translatedFormat('d F Y H:i'),
                'client_name' => $state['client_name'] ?? '-',
                'proposal_id' => $state['proposal_id'] ?? '-',
                'enquiry' => $state['enquiry'] ?? '-',
                'proposal_for' => $state['proposal_for'] ?? '-',
            ],
            'data' => $data,
        ];
    }

    protected function groupByTicket(Collection $tickets): array
    {
        $state = $this->form->getState();

        return $tickets
            ->map(function (Ticket $ticket) use ($state) {
                $allProgresses = $this->parseProgresses($ticket->ticket_progress);
                $filteredProgresses = $this->filterProgressesByDate($allProgresses, $state);

                $clientFiles = $this->extractClientFiles($ticket);
                $progressDocuments = array_map(
                    fn($p) => $this->resolveProgressDocument($p),
                    $filteredProgresses
                );

                $documents = [];

                if (!empty($clientFiles)) {
                    $documents[] = [
                        'text' => '',
                        'embedded_images' => [],
                        'thumbnail_files' => [],
                        'other_files' => $clientFiles,
                        'pdf_files' => [],
                        'timestamp' => $ticket->created_at?->toISOString() ?? now()->toISOString(),
                        'is_client_document' => true,
                        'is_progress' => false,
                    ];
                }

                foreach ($progressDocuments as $progressDoc) {
                    $progressDoc['is_progress'] = true;
                    $progressDoc['is_client_document'] = false;
                    $documents[] = $progressDoc;
                }

                if (empty($documents)) return null;

                $progressCount = count($progressDocuments);

                return [
                    'ticket_code' => $ticket->ticket_code,
                    'ticket_title' => $ticket->ticket_title,
                    'status' => $ticket->status ?? '',
                    'created_at' => $ticket->created_at?->format('d/m/Y H:i') ?? '-',
                    'documents_count' => $progressCount,
                    'has_client_files' => !empty($clientFiles),
                    'documents' => $documents,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    protected function extractClientFiles(Ticket $ticket): array
    {
        $clientFiles = [];

        if (empty($ticket->ticket_document_support)) {
            return $clientFiles;
        }

        $rawClientFiles = $ticket->ticket_document_support;

        if (is_string($rawClientFiles)) {
            $decoded = json_decode($rawClientFiles, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawClientFiles = $decoded;
            } else {
                $rawClientFiles = [$rawClientFiles];
            }
        }

        if (!is_array($rawClientFiles)) {
            $rawClientFiles = [$rawClientFiles];
        }

        foreach ($rawClientFiles as $cf) {
            if (empty($cf)) continue;

            if (is_string($cf)) {
                $filePath = $cf;
                $fullPath = 'public/' . ltrim($filePath, '/');
                $fileSize = Storage::exists($fullPath) ? Storage::size($fullPath) : 0;

                $clientFiles[] = [
                    'url' => asset('storage/' . $filePath),
                    'name' => basename($filePath),
                    'ext' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
                    'size_formatted' => $fileSize > 0 ? self::formatFileSize($fileSize) : '',
                    'type' => 'client',
                    'file' => $filePath,
                ];
            } elseif (is_array($cf)) {
                $filePath = $cf['path'] ?? $cf['url'] ?? '';
                $url = $cf['url'] ?? (isset($cf['path']) ? asset('storage/' . $cf['path']) : '#');
                $fileName = $cf['name'] ?? basename($url);

                $fullPath = 'public/' . ltrim($filePath, '/');
                $fileSize = Storage::exists($fullPath) ? Storage::size($fullPath) : 0;

                $clientFiles[] = [
                    'url' => $url,
                    'name' => $fileName,
                    'ext' => $cf['ext'] ?? strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                    'size_formatted' => $cf['size_formatted'] ?? ($fileSize > 0 ? self::formatFileSize($fileSize) : ''),
                    'type' => 'client',
                    'file' => $filePath,
                ];
            }
        }

        return $clientFiles;
    }

    protected function resolveProgressDocument(array $p): array
    {
        $rawHtml = $p['progress'] ?? '';
        $allFiles = is_array($p['file'] ?? []) ? ($p['file'] ?? []) : [$p['file'] ?? ''];

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $rawHtml, $matches);
        $embeddedImageUrls = $matches[1] ?? [];

        $embeddedImages = [];
        foreach ($embeddedImageUrls as $url) {
            $localPath = $this->convertUrlToLocalPath($url);
            if ($localPath && file_exists($localPath)) {
                $embeddedImages[] = [
                    'url' => $url,
                    'path' => $localPath,
                    'name' => basename($localPath),
                    'ext' => strtolower(pathinfo($localPath, PATHINFO_EXTENSION))
                ];
            }
        }

        $cleanText = $this->sanitiseProgressHtml($rawHtml);

        $videoExt = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv'];
        $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];

        $thumbnailFiles = [];
        $otherFiles = [];
        $pdfFiles = [];

        foreach ($allFiles as $file) {
            if (empty($file)) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $filePath = 'public/' . $file;
            $exists = Storage::exists($filePath);
            $fileSize = $exists ? Storage::size($filePath) : 0;
            $localPath = $exists ? Storage::path($filePath) : null;

            $meta = [
                'file' => $file,
                'url' => asset('storage/' . $file),
                'name' => basename($file),
                'ext' => $ext,
                'size_bytes' => $fileSize,
                'size_formatted' => $fileSize > 0 ? self::formatFileSize($fileSize) : '',
                'local_path' => $localPath,
                'type' => 'internal',
            ];

            if ($ext === 'pdf') {
                $pdfFiles[] = $meta;
            } elseif (in_array($ext, $videoExt) || (in_array($ext, $imageExt) && $fileSize < 5_000_000)) {
                $thumbnailFiles[] = $meta;
            } else {
                $otherFiles[] = $meta;
            }
        }

        return [
            'text' => $cleanText,
            'embedded_images' => $embeddedImages,
            'thumbnail_files' => $thumbnailFiles,
            'other_files' => $otherFiles,
            'pdf_files' => $pdfFiles,
            'timestamp' => $p['timestamp'] ?? null,
        ];
    }

    protected function convertUrlToLocalPath(string $url): ?string
    {
        if (str_contains($url, '/storage/')) {
            $relativePath = substr($url, strpos($url, '/storage/') + 9);
            $localPath = storage_path('app/public/' . $relativePath);
            if (file_exists($localPath)) {
                return $localPath;
            }
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '';
            if (str_contains($path, '/storage/')) {
                $relativePath = substr($path, strpos($path, '/storage/') + 9);
                $localPath = storage_path('app/public/' . $relativePath);
                if (file_exists($localPath)) {
                    return $localPath;
                }
            }
        }

        $directPath = storage_path('app/public/' . ltrim($url, '/'));
        if (file_exists($directPath)) {
            return $directPath;
        }

        return null;
    }

    protected function sanitiseProgressHtml(string $html): string
    {
        $cleaned = preg_replace(
            [
                '/<img[^>]+>/i',
                '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                '/[\w\-\.]+\.(jpg|jpeg|png|gif|webp|svg|bmp)\s*[\d\.,]+\s*(B|KB|MB|GB|TB)/i',
                '/<p[^>]*>\s*<\/p>/i',
            ],
            '',
            $html
        );

        $allowed = '<p><br><strong><em><ul><ol><li><a><span><h1><h2><h3><h4><h5><h6><blockquote><code><pre>';
        $cleaned = strip_tags($cleaned, $allowed);

        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }

    public function resetFilters(): void
    {
        $this->form->fill($this->defaultFormState());
        $this->clearReport();

        Notification::make()
            ->title('Filter Direset')
            ->body('Semua filter telah dikembalikan ke pengaturan awal.')
            ->info()
            ->send();
    }

    protected function clearReport(): void
    {
        $this->hasReport = false;
        $this->reportSummary = [];
        $this->debugInfo = [];
        cache()->forget($this->cacheKey);
    }

    public function exportReport(): void
    {
        if (!$this->hasReport) {
            Notification::make()
                ->title('Informasi')
                ->body('Tidak ada data untuk diexport. Tampilkan laporan terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $reportData = cache()->get($this->cacheKey, []);

        if (empty($reportData)) {
            Notification::make()
                ->title('Informasi')
                ->body('Sesi laporan telah habis. Silakan tampilkan laporan kembali.')
                ->warning()
                ->send();
            return;
        }

        cache()->put($this->cacheKey . '_summary', $reportData['summary'] ?? [], now()->addMinutes(5));
        cache()->put($this->cacheKey . '_data', $reportData['data'] ?? [], now()->addMinutes(5));
        cache()->put($this->cacheKey . '_notes', $this->data['admin_notes'] ?? '', now()->addMinutes(5));

        $this->dispatch('open-download-url', url: route('report.download', [
            'key' => $this->cacheKey
        ]));
    }

    // =========================================================================
    // RENDER PDF
    // =========================================================================

    public static function renderPdfStatic(array $summary, array $data, ?string $notes): string
    {
        $attachedPdfs = [];

        foreach ($data as $ticket) {
            foreach ($ticket['documents'] ?? [] as $doc) {
                $isClientDoc = $doc['is_client_document'] ?? false;

                if ($isClientDoc) {
                    foreach ($doc['other_files'] ?? [] as $cf) {
                        if (($cf['ext'] ?? '') === 'pdf') {
                            $localPath = null;
                            $url = $cf['url'] ?? '';

                            if (!empty($url) && str_contains($url, '/storage/')) {
                                $relativePath = substr($url, strpos($url, '/storage/') + 9);
                                $possiblePath = storage_path('app/public/' . $relativePath);
                                if (file_exists($possiblePath)) {
                                    $localPath = $possiblePath;
                                }
                            }

                            if (!$localPath && isset($cf['file'])) {
                                $possiblePath = storage_path('app/public/' . ltrim($cf['file'], '/'));
                                if (file_exists($possiblePath)) {
                                    $localPath = $possiblePath;
                                }
                            }

                            if ($localPath && file_exists($localPath)) {
                                $attachedPdfs[] = [
                                    'name' => $cf['name'] ?? basename($localPath),
                                    'path' => $localPath,
                                    'ticket_code' => $ticket['ticket_code'] ?? '-',
                                    'type' => 'client_document',
                                ];
                            }
                        }
                    }
                    continue;
                }

                foreach ($doc['pdf_files'] ?? [] as $pf) {
                    $localPath = null;
                    if (isset($pf['local_path']) && $pf['local_path'] && file_exists($pf['local_path'])) {
                        $localPath = $pf['local_path'];
                    } elseif (isset($pf['file']) && $pf['file']) {
                        $possiblePath = storage_path('app/public/' . ltrim($pf['file'], '/'));
                        if (file_exists($possiblePath)) {
                            $localPath = $possiblePath;
                        }
                    }
                    if ($localPath && file_exists($localPath)) {
                        $attachedPdfs[] = [
                            'name' => $pf['name'] ?? basename($localPath),
                            'path' => $localPath,
                            'ticket_code' => $ticket['ticket_code'] ?? '-',
                            'type' => 'progress_document',
                        ];
                    }
                }
            }
        }

        $pdf = new class($summary, $data, $notes, $attachedPdfs) extends \setasign\Fpdi\Fpdi {
            private array $summary;
            private array $data;
            private ?string $notes;
            private array $attachedPdfs;
            private float $leftMargin = 15;
            private float $rightMargin = 15;
            private float $topMargin = 20;
            private float $bottomMargin = 20;
            private float $pageWidth;

            public function __construct(array $summary, array $data, ?string $notes, array $attachedPdfs)
            {
                parent::__construct('P', 'mm', 'A4');
                $this->summary = $summary;
                $this->data = $data;
                $this->notes = $notes;
                $this->attachedPdfs = $attachedPdfs;
                $this->pageWidth = 210 - $this->leftMargin - $this->rightMargin;

                $this->SetAutoPageBreak(false, $this->bottomMargin);
                $this->SetMargins($this->leftMargin, $this->topMargin, $this->rightMargin);
                $this->AddPage();

                $this->buildHeader();
                if (!empty($this->notes)) {
                    $this->buildNotes();
                }
                $this->buildTickets();

                if (!empty($this->attachedPdfs)) {
                    $this->appendAttachedPdfs();
                }
            }

            private function appendAttachedPdfs(): void
            {
                $this->AddPage();
                $this->drawSeparatorPage();

                $groupedPdfs = [];
                foreach ($this->attachedPdfs as $att) {
                    $ticketCode = $att['ticket_code'];
                    if (!isset($groupedPdfs[$ticketCode])) {
                        $groupedPdfs[$ticketCode] = [];
                    }
                    $groupedPdfs[$ticketCode][] = $att;
                }

                foreach ($groupedPdfs as $ticketCode => $pdfs) {
                    foreach ($pdfs as $att) {
                        try {
                            $pageCount = $this->setSourceFile($att['path']);
                            $typeLabel = ($att['type'] ?? '') === 'client_document' ? 'Dokumen Client' : 'Dokumen Progress';

                            for ($p = 1; $p <= $pageCount; $p++) {
                                $tpl = $this->importPage($p);
                                $size = $this->getTemplateSize($tpl);
                                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                                $this->AddPage($orientation);

                                $this->setFont('Helvetica', 'B', 8);
                                $this->setFillColor(243, 244, 246);
                                $this->Rect(0, 0, $orientation === 'L' ? 297 : 210, 14, 'F');
                                $this->setY(3);
                                $this->setX($this->leftMargin);
                                $this->Cell(0, 5, mb_convert_encoding($att['name'], 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
                                $this->setFont('Helvetica', 'I', 7);
                                $this->setX(-75);
                                $this->Cell(30, 5, $typeLabel, 0, 0, 'R');
                                $this->setX(-45);
                                $this->setFont('Helvetica', '', 7);
                                $this->Cell(35, 5, $att['ticket_code'] . ' | Page ' . $p . '/' . $pageCount, 0, 0, 'R');

                                $margin = 10;
                                $headerH = 16;
                                $pageW = $orientation === 'L' ? 297 : 210;
                                $pageH = $orientation === 'L' ? 210 : 297;
                                $availW = $pageW - ($margin * 2);
                                $availH = $pageH - $headerH - $margin;
                                $scale = min($availW / $size['width'], $availH / $size['height'], 1.0);
                                $drawW = $size['width'] * $scale;
                                $drawH = $size['height'] * $scale;
                                $drawX = $margin + (($availW - $drawW) / 2);
                                $drawY = $headerH + 2;
                                $this->useTemplate($tpl, $drawX, $drawY, $drawW, $drawH);
                            }
                        } catch (\Exception $e) {
                            $this->AddPage();
                            $this->setY(80);
                            $this->setFont('Helvetica', 'B', 12);
                            $this->setTextColor(185, 28, 28);
                            $this->Cell(0, 10, 'Cannot display: ' . mb_convert_encoding($att['name'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
                        }
                    }
                }
            }

            private function drawSeparatorPage(): void
            {
                $this->setFillColor(249, 250, 251);
                $this->Rect(0, 0, 210, 297, 'F');
                $this->setFillColor(31, 41, 55);
                $this->Rect(0, 100, 210, 1.5, 'F');
                $this->setFont('Helvetica', 'B', 28);
                $this->setTextColor(17, 24, 39);
                $this->setY(108);
                $this->Cell(0, 14, 'LAMPIRAN PDF', 0, 1, 'C');
                $this->setFont('Helvetica', '', 11);
                $this->setTextColor(107, 114, 128);
                $this->setY(124);
                $this->Cell(0, 6, 'Dokumen PDF yang dilampirkan pada laporan ini', 0, 1, 'C');
                $this->setFillColor(31, 41, 55);
                $this->Rect(0, 132, 210, 1.5, 'F');
                $this->setY(145);
                $this->setFont('Helvetica', 'B', 10);
                $this->setTextColor(55, 65, 81);
                $this->setX($this->leftMargin);
                $this->Cell(0, 8, 'Daftar Lampiran:', 0, 1, 'L');

                $y = $this->GetY();
                foreach ($this->attachedPdfs as $i => $att) {
                    if ($y > 260) {
                        $this->AddPage();
                        $y = $this->GetY();
                    }
                    $this->setFont('Helvetica', '', 9);
                    $this->setTextColor(31, 41, 55);
                    $this->setX($this->leftMargin);
                    $this->Cell(10, 6, ($i + 1) . '.', 0, 0, 'L');
                    $name = strlen($att['name']) > 55 ? substr($att['name'], 0, 52) . '...' : $att['name'];
                    $this->Cell(120, 6, mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                    $typeLabel = ($att['type'] ?? '') === 'client_document' ? 'Client' : 'Progress';
                    $this->setFont('Helvetica', 'I', 7);
                    $this->setTextColor(139, 92, 246);
                    $this->setX($this->leftMargin + 135);
                    $this->Cell(25, 6, $typeLabel, 0, 0, 'L');

                    $this->setFont('Helvetica', '', 8);
                    $this->setTextColor(107, 114, 128);
                    $this->Cell(0, 6, '[' . $att['ticket_code'] . ']', 0, 1, 'R');
                    $y = $this->GetY();
                }
            }

            private function checkPageBreak(float $neededHeight): void
            {
                if ($this->GetY() + $neededHeight > (297 - $this->bottomMargin)) {
                    $this->AddPage();
                }
            }

            private function buildHeader(): void
            {
                // LOGO DI KIRI
                $logoPath = base_path('public/storage/meta/01KF044971QVAFZJTQ6V5MKX3T.png');
                $logoWidth = 20;

                if (file_exists($logoPath)) {
                    $this->Image($logoPath, $this->leftMargin, 10, $logoWidth);
                } else {
                    $this->setFont('Helvetica', 'B', 8);
                    $this->setTextColor(156, 163, 175);
                    $this->setXY($this->leftMargin, 14);
                    $this->Cell($logoWidth, 4, 'LOGO', 0, 0, 'C');
                }

                // TABEL KETERANGAN
                $tableStartX = $this->leftMargin + $logoWidth + 5;
                $col1Width = 30;
                $col2Width = 50;
                $col3Width = 32;
                $col4Width = 48;
                $rowHeight = 6.5;
                $startY = 10;
                $headerBg = [243, 244, 246];
                $rowBg = [255, 255, 255];

                // BARIS 1: ID Laporan | Klien
                $y = $startY;
                $this->setFillColor($headerBg[0], $headerBg[1], $headerBg[2]);
                $this->Rect($tableStartX, $y, $col1Width, $rowHeight, 'F');
                $this->setDrawColor(209, 213, 219);
                $this->Rect($tableStartX, $y, $col1Width, $rowHeight, 'D');
                $this->setFont('Helvetica', 'B', 6.5);
                $this->setTextColor(55, 65, 81);
                $this->setXY($tableStartX + 2, $y + 1.5);
                $this->Cell($col1Width - 4, 4, 'ID Laporan', 0, 0, 'L');

                $x2 = $tableStartX + $col1Width;
                $this->setFillColor($rowBg[0], $rowBg[1], $rowBg[2]);
                $this->Rect($x2, $y, $col2Width, $rowHeight, 'F');
                $this->Rect($x2, $y, $col2Width, $rowHeight, 'D');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(31, 41, 55);
                $this->setXY($x2 + 2, $y + 1.5);

                // Ambil ticket_code dari data tiket pertama
                $ticketCode = '-';
                if (!empty($this->data) && isset($this->data[0]['ticket_code'])) {
                    $ticketCode = $this->data[0]['ticket_code'];
                }
                $this->Cell($col2Width - 4, 4, mb_convert_encoding($ticketCode, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                $x3 = $x2 + $col2Width;
                $this->setFillColor($headerBg[0], $headerBg[1], $headerBg[2]);
                $this->Rect($x3, $y, $col3Width, $rowHeight, 'F');
                $this->Rect($x3, $y, $col3Width, $rowHeight, 'D');
                $this->setFont('Helvetica', 'B', 6.5);
                $this->setTextColor(55, 65, 81);
                $this->setXY($x3 + 2, $y + 1.5);
                $this->Cell($col3Width - 4, 4, 'Klien', 0, 0, 'L');

                $x4 = $x3 + $col3Width;
                $this->setFillColor($rowBg[0], $rowBg[1], $rowBg[2]);
                $this->Rect($x4, $y, $col4Width, $rowHeight, 'F');
                $this->Rect($x4, $y, $col4Width, $rowHeight, 'D');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(31, 41, 55);
                $this->setXY($x4 + 2, $y + 1.5);
                $klien = mb_substr($this->summary['client_name'] ?? '-', 0, 20);
                $this->Cell($col4Width - 4, 4, mb_convert_encoding($klien, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                // BARIS 2: Pekerjaan (full width)
                $y = $startY + $rowHeight;
                $fullWidth = $col1Width + $col2Width + $col3Width + $col4Width;
                $this->setFillColor($headerBg[0], $headerBg[1], $headerBg[2]);
                $this->Rect($tableStartX, $y, 30, $rowHeight, 'F');
                $this->Rect($tableStartX, $y, 30, $rowHeight, 'D');
                $this->setFont('Helvetica', 'B', 6.5);
                $this->setTextColor(55, 65, 81);
                $this->setXY($tableStartX + 2, $y + 1.5);
                $this->Cell(26, 4, 'Pekerjaan', 0, 0, 'L');

                $this->setFillColor($rowBg[0], $rowBg[1], $rowBg[2]);
                $this->Rect($tableStartX + 30, $y, $fullWidth - 30, $rowHeight, 'F');
                $this->Rect($tableStartX + 30, $y, $fullWidth - 30, $rowHeight, 'D');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(31, 41, 55);
                $this->setXY($tableStartX + 33, $y + 1.5);
                $pekerjaan = mb_substr($this->summary['proposal_for'] ?? '-', 0, 45);
                $this->Cell($fullWidth - 35, 4, mb_convert_encoding($pekerjaan, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                // BARIS 3: Dilaporkan oleh | Periode Laporan
                $y = $startY + ($rowHeight * 2);
                $this->setFillColor($headerBg[0], $headerBg[1], $headerBg[2]);
                $this->Rect($tableStartX, $y, $col1Width, $rowHeight, 'F');
                $this->Rect($tableStartX, $y, $col1Width, $rowHeight, 'D');
                $this->setFont('Helvetica', 'B', 6.5);
                $this->setTextColor(55, 65, 81);
                $this->setXY($tableStartX + 2, $y + 1.5);
                $this->Cell($col1Width - 4, 4, 'Dilaporkan oleh', 0, 0, 'L');

                $this->setFillColor($rowBg[0], $rowBg[1], $rowBg[2]);
                $this->Rect($x2, $y, $col2Width, $rowHeight, 'F');
                $this->Rect($x2, $y, $col2Width, $rowHeight, 'D');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(31, 41, 55);
                $this->setXY($x2 + 2, $y + 1.5);
                // Gunakan format yang sudah termasuk timestamp
                $dilaporkanOleh = mb_substr($this->summary['generated_by'] ?? '-', 0, 30);
                $this->Cell($col2Width - 4, 4, mb_convert_encoding($dilaporkanOleh, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                $this->setFillColor($headerBg[0], $headerBg[1], $headerBg[2]);
                $this->Rect($x3, $y, $col3Width, $rowHeight, 'F');
                $this->Rect($x3, $y, $col3Width, $rowHeight, 'D');
                $this->setFont('Helvetica', 'B', 6.5);
                $this->setTextColor(55, 65, 81);
                $this->setXY($x3 + 2, $y + 1.5);
                $this->Cell($col3Width - 4, 4, 'Periode Laporan', 0, 0, 'L');

                $this->setFillColor($rowBg[0], $rowBg[1], $rowBg[2]);
                $this->Rect($x4, $y, $col4Width, $rowHeight, 'F');
                $this->Rect($x4, $y, $col4Width, $rowHeight, 'D');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(31, 41, 55);
                $this->setXY($x4 + 2, $y + 1.5);
                $periode = mb_substr($this->summary['date_range'] ?? '-', 0, 25);
                $this->Cell($col4Width - 4, 4, mb_convert_encoding($periode, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                // GARIS PEMISAH
                $this->setY($y + $rowHeight + 5);
                $this->setDrawColor(229, 231, 235);
                $this->Line($this->leftMargin, $this->GetY(), $this->leftMargin + $this->pageWidth, $this->GetY());
                $this->Ln(6);
            }

            private function buildNotes(): void
            {
                $this->heading('Summary / Catatan');
                $plainText = $this->plainText($this->notes ?? '');
                if (empty($plainText)) return;
                $this->setFont('Helvetica', '', 9);
                $this->setTextColor(55, 65, 81);
                $this->setDrawColor(229, 231, 235);
                $this->MultiCell($this->pageWidth, 5, mb_convert_encoding($plainText, 'ISO-8859-1', 'UTF-8'), 1, 'L');
                $this->Ln(5);
            }

            private function buildTickets(): void
            {
                $this->heading('Progres Pekerjaan');

                foreach ($this->data as $ticket) {
                    $docs = $ticket['documents'] ?? [];
                    $hasClientDocs = false;
                    $progressDocs = [];

                    foreach ($docs as $doc) {
                        if (($doc['is_client_document'] ?? false)) {
                            $hasClientDocs = true;
                        } else {
                            $progressDocs[] = $doc;
                        }
                    }

                    // TIMELINE DI KANAN
                    $timelineX = $this->leftMargin + $this->pageWidth - 18;
                    $cardStartX = $this->leftMargin;
                    $cardWidth = $this->pageWidth - 16;

                    // CLIENT DOCUMENTS
                    if ($hasClientDocs) {
                        $this->setFont('Helvetica', 'B', 9);
                        $this->setTextColor(139, 92, 246);
                        $this->setX($this->leftMargin + 4);
                        $this->Cell(0, 8, 'Dokumen Pendukung Client', 0, 1, 'L');
                        $this->setDrawColor(221, 214, 254);
                        $this->Line($this->leftMargin + 4, $this->GetY(), $this->leftMargin + $this->pageWidth - 4, $this->GetY());
                        $this->Ln(4);

                        $clientDocsArray = array_values(array_filter($docs, fn($d) => $d['is_client_document'] ?? false));

                        $startY = $this->GetY();
                        $totalHeight = 0;
                        foreach ($clientDocsArray as $doc) {
                            $totalHeight += $this->calculateClientDocumentHeight($doc);
                        }

                        // Garis vertikal utama
                        $this->setDrawColor(229, 231, 235);
                        $this->Line($timelineX, $startY - 2, $timelineX, $startY + $totalHeight);

                        $currentY = $startY;
                        foreach ($clientDocsArray as $idx => $doc) {
                            $isLatest = $idx === count($clientDocsArray) - 1;
                            $height = $this->drawClientDocumentAtPosition($doc, $isLatest, $currentY, $timelineX, $cardStartX, $cardWidth);
                            $currentY += $height;
                        }

                        $this->setY($startY + $totalHeight);
                        $this->Ln(4);
                    }

                    // PROGRESS DOCUMENTS
                    if (!empty($progressDocs)) {
                        $this->setFont('Helvetica', 'B', 9);
                        $this->setTextColor(31, 41, 55);
                        $this->setX($this->leftMargin + 4);
                        $this->Cell(0, 8, 'Riwayat Progress', 0, 1, 'L');
                        $this->setDrawColor(229, 231, 235);
                        $this->Line($this->leftMargin + 4, $this->GetY(), $this->leftMargin + $this->pageWidth - 4, $this->GetY());
                        $this->Ln(4);

                        $startY = $this->GetY();
                        $totalHeight = 0;
                        foreach ($progressDocs as $doc) {
                            $totalHeight += $this->calculateProgressDocumentHeight($doc);
                        }

                        // Garis vertikal utama
                        $this->setDrawColor(229, 231, 235);
                        $this->Line($timelineX, $startY - 2, $timelineX, $startY + $totalHeight);

                        $currentY = $startY;
                        foreach ($progressDocs as $idx => $doc) {
                            $isLatest = $idx === count($progressDocs) - 1;
                            $height = $this->drawProgressDocumentAtPosition($doc, $isLatest, $currentY, $timelineX, $cardStartX, $cardWidth);
                            $currentY += $height;
                        }

                        $this->setY($startY + $totalHeight);
                    }

                    $this->Ln(4);
                    $this->setDrawColor(229, 231, 235);
                    $this->Line($this->leftMargin, $this->GetY(), $this->leftMargin + $this->pageWidth, $this->GetY());
                    $this->Ln(6);
                }
            }

            private function calculateClientDocumentHeight(array $doc): float
            {
                $text = $this->plainText($doc['text'] ?? '');
                $textHeight = empty($text) ? 0 : (count(explode("\n", wordwrap($text, 85, "\n"))) * 4);

                $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                $thumbnailFiles = [];
                $otherFiles = [];

                foreach ($doc['other_files'] ?? [] as $cf) {
                    $ext = strtolower($cf['ext'] ?? '');
                    if (in_array($ext, $imageExt)) {
                        $thumbnailFiles[] = $cf;
                    } else {
                        $otherFiles[] = $cf;
                    }
                }

                $hasImages = !empty($thumbnailFiles);
                $imagesHeight = $hasImages ? 26 : 0;
                $otherCount = count($otherFiles);
                $filesHeight = min($otherCount, 5) * 9;

                return 20 + $textHeight + $imagesHeight + $filesHeight + 15;
            }

            private function calculateProgressDocumentHeight(array $doc): float
            {
                $text = $this->plainText($doc['text'] ?? '');
                $textHeight = empty($text) ? 0 : (count(explode("\n", wordwrap($text, 85, "\n"))) * 4);

                $thumbCount = count($doc['thumbnail_files'] ?? []);
                $embeddedCount = count($doc['embedded_images'] ?? []);
                $hasImages = ($thumbCount > 0 || $embeddedCount > 0);
                $imagesHeight = $hasImages ? 26 : 0;

                $otherCount = count($doc['other_files'] ?? []);
                $pdfCount = count($doc['pdf_files'] ?? []);
                $filesHeight = (min($otherCount + $pdfCount, 5)) * 9;

                return 20 + $textHeight + $imagesHeight + $filesHeight + 15;
            }

            private function drawClientDocumentAtPosition(array $doc, bool $isLatest, float $startY, float $timelineX, float $cardStartX, float $cardWidth): float
            {
                $text = $this->plainText($doc['text'] ?? '');
                $textHeight = empty($text) ? 0 : (count(explode("\n", wordwrap($text, 85, "\n"))) * 4);

                $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                $thumbnailFiles = [];
                $otherFiles = [];

                foreach ($doc['other_files'] ?? [] as $cf) {
                    $ext = strtolower($cf['ext'] ?? '');
                    if (in_array($ext, $imageExt)) {
                        $thumbnailFiles[] = $cf;
                    } else {
                        $otherFiles[] = $cf;
                    }
                }

                $hasImages = !empty($thumbnailFiles);
                $imagesHeight = $hasImages ? 26 : 0;
                $otherCount = count($otherFiles);
                $filesHeight = min($otherCount, 5) * 9;

                $this->setY($startY);
                $currentY = $this->GetY();

                // DOT TIMELINE DI KANAN
                $dotX = $timelineX - 2;
                $dotY = $currentY + 4;
                $this->setFont('ZapfDingbats', '', 8);
                if ($isLatest) {
                    $this->setTextColor(55, 65, 81);
                    $this->setXY($dotX - 1, $dotY - 3);
                    $this->Cell(5, 5, 'l', 0, 0, 'C');
                } else {
                    $this->setTextColor(209, 213, 219);
                    $this->setXY($dotX - 1, $dotY - 3);
                    $this->Cell(5, 5, 'o', 0, 0, 'C');
                }

                // BADGE
                $badgeText = 'Dokumen Client';
                $badgeWidth = 45;
                $this->setFillColor(243, 244, 246);
                $this->setDrawColor(229, 231, 235);
                $this->Rect($cardStartX + 6, $currentY, $badgeWidth, 5.5, 'FD');
                $this->setTextColor(107, 114, 128);
                $this->setFont('Helvetica', 'B', 7);
                $this->setXY($cardStartX + 10, $currentY + 1);
                $this->Cell($badgeWidth - 8, 4, $badgeText, 0, 0, 'L');

                // TIMESTAMP
                $timestamp = Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(156, 163, 175);
                $this->setXY($cardStartX + $cardWidth - 55, $currentY + 1);
                $this->Cell(50, 4, $timestamp, 0, 0, 'R');
                $this->setY($currentY + 9);

                // TEXT
                if (!empty($text)) {
                    $this->setFont('Helvetica', '', 8);
                    $this->setTextColor(55, 65, 81);
                    foreach (explode("\n", wordwrap($text, 85, "\n")) as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        $this->checkPageBreak(5);
                        $this->setX($cardStartX + 6);
                        $this->Cell($cardWidth - 12, 4, mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
                    }
                    $this->Ln(2);
                }

                // THUMBNAILS
                if (!empty($thumbnailFiles)) {
                    $thumbW = 28;
                    $thumbH = 22;
                    $thumbX = $cardStartX + 6;
                    $thumbY = $this->GetY();

                    foreach (array_slice($thumbnailFiles, 0, 4) as $i => $thumb) {
                        $posX = $thumbX + ($i * ($thumbW + 3));
                        $this->setDrawColor(229, 231, 235);
                        $this->Rect($posX, $thumbY, $thumbW, $thumbH, 'D');

                        $imagePath = null;
                        if (isset($thumb['local_path']) && file_exists($thumb['local_path'])) {
                            $imagePath = $thumb['local_path'];
                        } elseif (isset($thumb['file'])) {
                            $possiblePath = storage_path('app/public/' . ltrim($thumb['file'], '/'));
                            if (file_exists($possiblePath)) {
                                $imagePath = $possiblePath;
                            }
                        } elseif (isset($thumb['url'])) {
                            $possiblePath = $this->urlToLocalPath($thumb['url']);
                            if ($possiblePath && file_exists($possiblePath)) {
                                $imagePath = $possiblePath;
                            }
                        }

                        if ($imagePath && file_exists($imagePath)) {
                            try {
                                $this->Image($imagePath, $posX, $thumbY, $thumbW, $thumbH);
                            } catch (\Exception $e) {
                                $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $thumb['ext'] ?? 'IMG');
                            }
                        } else {
                            $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $thumb['ext'] ?? 'IMG');
                        }
                    }
                    $this->setY($thumbY + $thumbH + 4);
                }

                // OTHER FILES
                if (!empty($otherFiles)) {
                    foreach (array_slice($otherFiles, 0, 5) as $lf) {
                        $this->checkPageBreak(9);
                        $ry = $this->GetY();

                        $this->setFillColor(245, 243, 255);
                        $this->setDrawColor(221, 214, 254);
                        $this->Rect($cardStartX + 6, $ry, $cardWidth - 12, 8, 'FD');

                        $this->setFillColor(229, 231, 235);
                        $this->Rect($cardStartX + 10, $ry + 1.5, 14, 5, 'F');
                        $this->setFont('Helvetica', 'B', 5.5);
                        $this->setTextColor(55, 65, 81);
                        $this->setXY($cardStartX + 13, $ry + 2.5);
                        $fileExt = strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3));
                        $this->Cell(8, 4, $fileExt, 0, 0, 'C');

                        $this->setFont('Helvetica', '', 7);
                        $this->setTextColor(55, 65, 81);
                        $this->setXY($cardStartX + 28, $ry + 2.5);
                        $name = strlen($lf['name'] ?? '') > 45 ? substr($lf['name'], 0, 42) . '...' : ($lf['name'] ?? '-');
                        $this->Cell(80, 4, mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                        $this->setFont('Helvetica', 'I', 5.5);
                        $this->setTextColor(139, 92, 246);
                        $this->setXY($cardStartX + 112, $ry + 2.5);
                        $this->Cell(20, 4, 'Client Doc', 0, 0, 'L');

                        if (!empty($lf['size_formatted'])) {
                            $this->setFont('Helvetica', '', 6);
                            $this->setTextColor(156, 163, 175);
                            $this->setXY($cardStartX + $cardWidth - 45, $ry + 2.5);
                            $this->Cell(35, 4, $lf['size_formatted'], 0, 0, 'R');
                        }
                        $this->setY($ry + 9);
                    }
                }

                $this->Ln(4);
                $divY = $this->GetY();
                $this->setDrawColor(243, 244, 246);
                $this->Line($cardStartX + 6, $divY, $cardStartX + $cardWidth - 6, $divY);

                $totalHeight = $divY - $startY + 6;
                return $totalHeight;
            }

            private function drawProgressDocumentAtPosition(array $doc, bool $isLatest, float $startY, float $timelineX, float $cardStartX, float $cardWidth): float
            {
                $text = $this->plainText($doc['text'] ?? '');
                $textHeight = empty($text) ? 0 : (count(explode("\n", wordwrap($text, 85, "\n"))) * 4);

                $thumbCount = count($doc['thumbnail_files'] ?? []);
                $embeddedCount = count($doc['embedded_images'] ?? []);
                $hasImages = ($thumbCount > 0 || $embeddedCount > 0);
                $imagesHeight = $hasImages ? 26 : 0;

                $otherCount = count($doc['other_files'] ?? []);
                $pdfCount = count($doc['pdf_files'] ?? []);
                $filesHeight = (min($otherCount + $pdfCount, 5)) * 9;

                $this->setY($startY);
                $currentY = $this->GetY();

                // DOT TIMELINE DI KANAN
                $dotX = $timelineX - 2;
                $dotY = $currentY + 4;
                $this->setFont('ZapfDingbats', '', 8);
                if ($isLatest) {
                    $this->setTextColor(55, 65, 81);
                    $this->setXY($dotX - 1, $dotY - 3);
                    $this->Cell(5, 5, 'l', 0, 0, 'C');
                } else {
                    $this->setTextColor(209, 213, 219);
                    $this->setXY($dotX - 1, $dotY - 3);
                    $this->Cell(5, 5, 'o', 0, 0, 'C');
                }

                // BADGE
                $badgeText = $isLatest ? 'Terbaru' : 'Sebelumnya';
                $badgeWidth = 35;
                if ($isLatest) {
                    $this->setDrawColor(55, 65, 81);
                    $this->Rect($cardStartX + 6, $currentY, $badgeWidth, 5.5, 'D');
                    $this->setTextColor(31, 41, 55);
                    $this->setFont('Helvetica', 'B', 7);
                } else {
                    $this->setFillColor(243, 244, 246);
                    $this->setDrawColor(229, 231, 235);
                    $this->Rect($cardStartX + 6, $currentY, $badgeWidth, 5.5, 'FD');
                    $this->setTextColor(107, 114, 128);
                    $this->setFont('Helvetica', '', 7);
                }
                $this->setXY($cardStartX + 10, $currentY + 1);
                $this->Cell($badgeWidth - 8, 4, $badgeText, 0, 0, 'L');

                // TIMESTAMP
                $timestamp = Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i');
                $this->setFont('Helvetica', '', 6.5);
                $this->setTextColor(156, 163, 175);
                $this->setXY($cardStartX + $cardWidth - 55, $currentY + 1);
                $this->Cell(50, 4, $timestamp, 0, 0, 'R');
                $this->setY($currentY + 9);

                // TEXT - dengan bullet points
                if (!empty($text)) {
                    $this->setFont('Helvetica', '', 8);
                    $this->setTextColor(55, 65, 81);
                    $lines = explode("\n", $text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        $this->checkPageBreak(5);
                        $this->setX($cardStartX + 6);
                        // Tambahkan bullet point jika line dimulai dengan "- "
                        if (str_starts_with($line, '- ')) {
                            $this->Cell(4, 4, '•', 0, 0, 'L');
                            $this->setX($cardStartX + 10);
                            $this->Cell($cardWidth - 16, 4, mb_convert_encoding(substr($line, 2), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
                        } else {
                            $this->Cell($cardWidth - 12, 4, mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
                        }
                    }
                    $this->Ln(2);
                }

                // THUMBNAILS
                if ($thumbCount > 0) {
                    $thumbW = 28;
                    $thumbH = 22;
                    $thumbX = $cardStartX + 6;
                    $thumbY = $this->GetY();

                    foreach (array_slice($doc['thumbnail_files'], 0, 4) as $i => $tf) {
                        $posX = $thumbX + ($i * ($thumbW + 3));
                        $this->setDrawColor(229, 231, 235);
                        $this->Rect($posX, $thumbY, $thumbW, $thumbH, 'D');

                        $imagePath = null;
                        if (isset($tf['local_path']) && $tf['local_path'] && file_exists($tf['local_path'])) {
                            $imagePath = $tf['local_path'];
                        } elseif (isset($tf['file']) && $tf['file']) {
                            $possiblePath = storage_path('app/public/' . ltrim($tf['file'], '/'));
                            if (file_exists($possiblePath)) {
                                $imagePath = $possiblePath;
                            }
                        }

                        if ($imagePath && file_exists($imagePath)) {
                            try {
                                $this->Image($imagePath, $posX, $thumbY, $thumbW, $thumbH);
                            } catch (\Exception $e) {
                                $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $tf['ext'] ?? 'IMG');
                            }
                        } else {
                            $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $tf['ext'] ?? 'IMG');
                        }
                    }
                    $this->setY($thumbY + $thumbH + 4);
                }

                // EMBEDDED IMAGES
                if ($embeddedCount > 0) {
                    $thumbW = 28;
                    $thumbH = 22;
                    $thumbX = $cardStartX + 6;
                    $thumbY = $this->GetY();

                    foreach (array_slice($doc['embedded_images'], 0, 4) as $i => $img) {
                        $posX = $thumbX + ($i * ($thumbW + 3));
                        $this->setDrawColor(229, 231, 235);
                        $this->Rect($posX, $thumbY, $thumbW, $thumbH, 'D');

                        $imagePath = $img['path'] ?? null;
                        if ($imagePath && file_exists($imagePath)) {
                            try {
                                $this->Image($imagePath, $posX, $thumbY, $thumbW, $thumbH);
                            } catch (\Exception $e) {
                                $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $img['ext'] ?? 'IMG');
                            }
                        } else {
                            $this->drawPlaceholder($posX, $thumbY, $thumbW, $thumbH, $img['ext'] ?? 'IMG');
                        }
                    }
                    $this->setY($thumbY + $thumbH + 4);
                }

                // PDF FILES
                if (!empty($doc['pdf_files'])) {
                    foreach (array_slice($doc['pdf_files'], 0, 5) as $pf) {
                        $this->checkPageBreak(9);
                        $ry = $this->GetY();

                        $this->setFillColor(254, 242, 242);
                        $this->setDrawColor(254, 202, 202);
                        $this->Rect($cardStartX + 6, $ry, $cardWidth - 12, 8, 'FD');

                        $this->setFillColor(254, 202, 202);
                        $this->Rect($cardStartX + 10, $ry + 1.5, 14, 5, 'F');
                        $this->setFont('Helvetica', 'B', 5.5);
                        $this->setTextColor(185, 28, 28);
                        $this->setXY($cardStartX + 13, $ry + 2.5);
                        $this->Cell(8, 4, 'PDF', 0, 0, 'C');

                        $this->setFont('Helvetica', '', 7);
                        $this->setTextColor(55, 65, 81);
                        $this->setXY($cardStartX + 28, $ry + 2.5);
                        $name = strlen($pf['name'] ?? '') > 45 ? substr($pf['name'], 0, 42) . '...' : ($pf['name'] ?? '-');
                        $this->Cell(80, 4, mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                        if (!empty($pf['size_formatted'])) {
                            $this->setFont('Helvetica', '', 6);
                            $this->setTextColor(156, 163, 175);
                            $this->setXY($cardStartX + $cardWidth - 45, $ry + 2.5);
                            $this->Cell(35, 4, $pf['size_formatted'], 0, 0, 'R');
                        }
                        $this->setY($ry + 9);
                    }
                }

                // OTHER FILES
                if (!empty($doc['other_files'])) {
                    foreach (array_slice($doc['other_files'], 0, 5) as $lf) {
                        $this->checkPageBreak(9);
                        $ry = $this->GetY();

                        $this->setFillColor(249, 250, 251);
                        $this->setDrawColor(243, 244, 246);
                        $this->Rect($cardStartX + 6, $ry, $cardWidth - 12, 8, 'FD');

                        $this->setFillColor(229, 231, 235);
                        $this->Rect($cardStartX + 10, $ry + 1.5, 14, 5, 'F');
                        $this->setFont('Helvetica', 'B', 5.5);
                        $this->setTextColor(55, 65, 81);
                        $this->setXY($cardStartX + 13, $ry + 2.5);
                        $fileExt = strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3));
                        $this->Cell(8, 4, $fileExt, 0, 0, 'C');

                        $this->setFont('Helvetica', '', 7);
                        $this->setTextColor(55, 65, 81);
                        $this->setXY($cardStartX + 28, $ry + 2.5);
                        $name = strlen($lf['name'] ?? '') > 45 ? substr($lf['name'], 0, 42) . '...' : ($lf['name'] ?? '-');
                        $this->Cell(80, 4, mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                        if (!empty($lf['size_formatted'])) {
                            $this->setFont('Helvetica', '', 6);
                            $this->setTextColor(156, 163, 175);
                            $this->setXY($cardStartX + $cardWidth - 45, $ry + 2.5);
                            $this->Cell(35, 4, $lf['size_formatted'], 0, 0, 'R');
                        }
                        $this->setY($ry + 9);
                    }
                }

                $this->Ln(4);
                $divY = $this->GetY();
                $this->setDrawColor(243, 244, 246);
                $this->Line($cardStartX + 6, $divY, $cardStartX + $cardWidth - 6, $divY);

                $totalHeight = $divY - $startY + 6;
                return $totalHeight;
            }

            private function urlToLocalPath(string $url): ?string
            {
                if (str_contains($url, '/storage/')) {
                    $relativePath = substr($url, strpos($url, '/storage/') + 9);
                    $localPath = storage_path('app/public/' . $relativePath);
                    if (file_exists($localPath)) {
                        return $localPath;
                    }
                }
                return null;
            }

            private function drawPlaceholder(float $x, float $y, float $w, float $h, string $ext): void
            {
                $this->setFillColor(249, 250, 251);
                $this->Rect($x, $y, $w, $h, 'F');
                $this->setFont('Helvetica', 'B', 6);
                $this->setTextColor(107, 114, 128);
                $this->setXY($x + 2, $y + ($h / 2) - 3);
                $this->Cell($w - 4, 4, strtoupper(substr($ext, 0, 3)), 0, 0, 'C');
            }

            private function heading(string $text): void
            {
                $this->checkPageBreak(10);
                $this->Ln(3);
                $this->setFont('Helvetica', 'B', 10);
                $this->setTextColor(31, 41, 55);
                $this->setDrawColor(209, 213, 219);
                $this->Cell($this->pageWidth, 6, $text, 'B', 1, 'L');
                $this->Ln(2);
            }

            private function getLines(string $text, float $width, string $family, string $style, float $size): int
            {
                $this->setFont($family, $style, $size);
                $encoded = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
                return ceil($this->GetStringWidth($encoded) / $width) + 1;
            }

            private function plainText(string $html): string
            {
                // Convert HTML to plain text with line breaks
                $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
                $html = preg_replace('/<\/p>/i', "\n", $html);
                $html = preg_replace('/<li[^>]*>/i', '- ', $html);
                $html = preg_replace('/<\/li>/i', "\n", $html);
                $html = strip_tags($html);
                // Decode HTML entities
                $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                return trim($html);
            }

            public function Footer(): void
            {
                $this->setY(-15);
                $this->setDrawColor(229, 231, 235);
                $this->Line($this->leftMargin, $this->GetY(), $this->leftMargin + $this->pageWidth, $this->GetY());
                $this->Ln(2);
                $this->setFont('Helvetica', '', 7.5);
                $this->setTextColor(156, 163, 175);

                // Gunakan ticket code di footer
                $ticketCode = '-';
                if (!empty($this->data) && isset($this->data[0]['ticket_code'])) {
                    $ticketCode = $this->data[0]['ticket_code'];
                }
                $leftText = 'Laporan Progress Tiket: ' . $ticketCode;

                $this->Cell($this->pageWidth / 2, 5, mb_convert_encoding($leftText, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
                $this->Cell($this->pageWidth / 2, 5, 'Halaman ' . $this->PageNo(), 0, 0, 'R');
            }
        };

        return $pdf->Output('S');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    protected function defaultFormState(): array
    {
        return [
            'selected_tickets' => null,
            'date_range' => null,
            'start_date' => null,
            'end_date' => null,
            'client_name' => null,
            'proposal_id' => null,
            'enquiry' => null,
            'proposal_for' => null,
            'generated_by' => null,
            'admin_notes' => null,
        ];
    }

    protected function parseProgresses(mixed $raw): array
    {
        if (is_array($raw)) return $raw;
        if (is_string($raw)) return json_decode($raw, true) ?? [];
        return [];
    }

    protected function getTicketOptions(): array
    {
        try {
            $tickets = Ticket::select('uuid', 'ticket_code', 'ticket_title', 'created_at')
                ->whereNotNull('uuid')
                ->whereNotNull('ticket_code')
                ->orderBy('ticket_code')
                ->get();

            $duplicateCodes = $tickets
                ->groupBy('ticket_code')
                ->filter(fn($group) => $group->count() > 1)
                ->keys()
                ->toArray();

            return $tickets
                ->mapWithKeys(function ($t) use ($duplicateCodes) {
                    $label = $t->ticket_code . ' - ' . $t->ticket_title;
                    if (in_array($t->ticket_code, $duplicateCodes)) {
                        $label .= ' (' . ($t->created_at?->format('d/m/Y H:i') ?? '-') . ' #' . $t->id . ')';
                    }
                    return [$t->uuid => $label];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading ticket options', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public static function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    public function getSummaryStats(): array
    {
        if (!$this->hasReport) return [];

        $reportData = cache()->get($this->cacheKey, []);
        $totalDocs = 0;

        foreach ($reportData['data'] ?? [] as $item) {
            $totalDocs += $item['documents_count'] ?? 0;
        }

        return [
            'total_tickets' => $this->reportSummary['total_tickets'] ?? 0,
            'total_documents' => $totalDocs,
        ];
    }
}
