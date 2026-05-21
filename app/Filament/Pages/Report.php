<?php

namespace App\Filament\Pages;

use \setasign\Fpdi\Fpdi;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'reportData'    => $reportData,
            'reportSummary' => $this->reportSummary,
            'debugInfo'     => $this->debugInfo,
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
                                        return 'Data client kosong, silakan isi manual';
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
                'client_name'  => null,
                'proposal_for' => null,
            ]));
            return;
        }

        $ticket = Ticket::where('uuid', $uuid)->first();
        if (!$ticket) return;

        $this->form->fill(array_merge($this->data ?? [], [
            'client_name'  => $ticket->ticket_name_client ?? '',
            'proposal_for' => $ticket->ticket_title ?? '-',
        ]));
    }

    public function generateReport(): void
    {
        $this->isLoading = true;

        try {
            Log::info('=== GENERATE REPORT START ===');

            $tickets = $this->getFilteredTickets();

            if ($tickets->isEmpty()) {
                Log::warning('No tickets found');
                Notification::make()->title('Informasi')
                    ->body('Tidak ada data tiket yang sesuai dengan filter yang dipilih.')
                    ->warning()->send();
                return;
            }

            Log::info('Tickets found', ['count' => $tickets->count()]);

            $reportData = $this->buildReport($tickets);

            if (empty($reportData['data'])) {
                Log::warning('No progress data in selected period');
                Notification::make()->title('Informasi')
                    ->body('Tidak ada progress tiket dalam periode yang dipilih.')
                    ->warning()->send();
                return;
            }

            cache()->put($this->cacheKey, $reportData, now()->addHours(2));
            $this->reportSummary = $reportData['summary'];
            $this->hasReport = true;

            Log::info('=== GENERATE REPORT SUCCESS ===', [
                'total_tickets' => $tickets->count(),
                'cache_key' => $this->cacheKey
            ]);

            Notification::make()->title('Sukses')
                ->body("Laporan berhasil ditampilkan. Total tiket: {$tickets->count()}")
                ->success()->send();
        } catch (\Exception $e) {
            Log::error('Error generating report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Notification::make()->title('Error')
                ->body('Gagal menampilkan laporan: ' . $e->getMessage())
                ->danger()->send();
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
            Log::info('Filtering by ticket UUID', ['uuid' => $state['selected_tickets']]);
        }

        $query->whereNotNull('ticket_progress')
            ->where('ticket_progress', '!=', '[]')
            ->where('ticket_progress', '!=', 'null');

        return $query->get();
    }

    protected function filterProgressesByDate(array $progresses, array $state): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($state);
        Log::info('Filtering progress by date range', [
            'start' => $startDate->toDateTimeString(),
            'end' => $endDate->toDateTimeString(),
            'total_progress' => count($progresses)
        ]);

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
                'today'      => [Carbon::today(), Carbon::today()],
                'yesterday'  => [Carbon::yesterday(), Carbon::yesterday()],
                'this_week'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
                'last_week'  => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
                'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
                'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
                'this_year'  => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
                'last_year'  => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
                default      => [Carbon::now()->startOfMonth(), Carbon::now()],
            };
            Log::info('Date range resolved (preset)', ['range' => $range]);
            return [$start->startOfDay(), $end->endOfDay()];
        }

        Log::info('Date range resolved (custom)', [
            'start' => $state['start_date'] ?? now()->startOfMonth(),
            'end' => $state['end_date'] ?? now()
        ]);

        return [
            Carbon::parse($state['start_date'] ?? now()->startOfMonth())->startOfDay(),
            Carbon::parse($state['end_date'] ?? now())->endOfDay(),
        ];
    }

    protected function updateDateRange(?string $range): void
    {
        if ($range === null || $range === 'custom') return;

        [$startDate, $endDate] = match ($range) {
            'today'      => [Carbon::today(), Carbon::today()],
            'yesterday'  => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week'  => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year'  => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year'  => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default      => [Carbon::now()->startOfMonth(), Carbon::now()],
        };

        $this->form->fill(array_merge($this->data, [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ]));
    }

    protected function buildReport(Collection $tickets): array
    {
        $state = $this->form->getState();
        $data  = $this->groupByTicket($tickets);

        $ticketCode = '-';
        if (!empty($data) && isset($data[0]['ticket_code'])) {
            $ticketCode = $data[0]['ticket_code'];
        }

        $generatedByName          = $state['generated_by'] ?? auth()->user()?->name ?? 'System';
        $generatedAt              = Carbon::now();
        $generatedByWithTimestamp = $generatedByName . '; ' . $generatedAt->translatedFormat('d F Y H:i');

        return [
            'summary' => [
                'total_tickets'       => count($data),
                'ticket_code'         => $ticketCode,
                'date_range'          => (function () use ($state) {
                    [$start, $end] = $this->resolveDateRange($state);
                    return $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y');
                })(),
                'generated_at'        => $generatedAt->translatedFormat('d M Y H:i:s'),
                'generated_by'        => $generatedByWithTimestamp,
                'generated_by_name'   => $generatedByName,
                'generated_timestamp' => $generatedAt->translatedFormat('d F Y H:i'),
                'client_name'         => $state['client_name'] ?? '-',
                'proposal_id'         => $state['proposal_id'] ?? '-',
                'enquiry'             => $state['enquiry'] ?? '-',
                'proposal_for'        => $state['proposal_for'] ?? '-',
            ],
            'data' => $data,
        ];
    }

    protected function groupByTicket(Collection $tickets): array
    {
        $state = $this->form->getState();

        return $tickets
            ->map(function (Ticket $ticket) use ($state) {
                Log::info('Processing ticket', [
                    'ticket_code' => $ticket->ticket_code,
                    'uuid' => $ticket->uuid
                ]);

                $allProgresses      = $this->parseProgresses($ticket->ticket_progress);
                $filteredProgresses = $this->filterProgressesByDate($allProgresses, $state);
                $clientFiles        = $this->extractClientFiles($ticket);

                // 🔥 KIRIM ticket_code KE resolveProgressDocument
                $progressDocuments = array_map(function ($p) use ($ticket) {
                    return $this->resolveProgressDocument($p, $ticket->ticket_code);
                }, $filteredProgresses);

                Log::info('Ticket files summary', [
                    'ticket_code' => $ticket->ticket_code,
                    'client_files_count' => count($clientFiles),
                    'progress_documents_count' => count($progressDocuments)
                ]);

                $documents = [];

                if (!empty($clientFiles)) {
                    $documents[] = [
                        'text'               => '',
                        'text_html'          => '',
                        'embedded_images'    => [],
                        'thumbnail_files'    => [],
                        'other_files'        => $clientFiles,
                        'pdf_files'          => [],
                        'timestamp'          => $ticket->created_at?->toISOString() ?? now()->toISOString(),
                        'is_client_document' => true,
                        'is_progress'        => false,
                    ];
                }

                foreach ($progressDocuments as $progressDoc) {
                    $progressDoc['is_progress']        = true;
                    $progressDoc['is_client_document'] = false;
                    $progressDoc['text']               = $progressDoc['text_html'];
                    $documents[] = $progressDoc;
                }

                if (empty($documents)) return null;

                return [
                    'ticket_code'      => $ticket->ticket_code,
                    'ticket_title'     => $ticket->ticket_title,
                    'status'           => $ticket->status ?? '',
                    'created_at'       => $ticket->created_at?->format('d/m/Y H:i') ?? '-',
                    'documents_count'  => count($progressDocuments),
                    'has_client_files' => !empty($clientFiles),
                    'documents'        => $documents,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Extract client files - langsung pakai ticket_code
     */
    protected function extractClientFiles(Ticket $ticket): array
    {
        $clientFiles = [];

        if (empty($ticket->ticket_document_support)) {
            return $clientFiles;
        }

        $rawClientFiles = $ticket->ticket_document_support;

        if (is_string($rawClientFiles)) {
            $decoded = json_decode($rawClientFiles, true);
            $rawClientFiles = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                ? $decoded
                : [$rawClientFiles];
        }

        if (!is_array($rawClientFiles)) {
            $rawClientFiles = [$rawClientFiles];
        }

        $ticketCode = $ticket->ticket_code;

        foreach ($rawClientFiles as $cf) {
            if (empty($cf)) continue;

            $fileName = '';

            if (is_string($cf)) {
                $fileName = basename($cf);
            } elseif (is_array($cf)) {
                $filePath = $cf['path'] ?? $cf['url'] ?? '';
                $fileName = $cf['name'] ?? basename($filePath);
            }

            if (empty($fileName)) continue;

            $localPath = storage_path("app/public/tickets/{$ticketCode}/{$fileName}");
            $fileExists = file_exists($localPath);

            $encodedFileName = rawurlencode($fileName);
            $url = asset("storage/tickets/{$ticketCode}/{$encodedFileName}");

            $fileSize = $fileExists ? filesize($localPath) : 0;

            $clientFiles[] = [
                'url'            => $url,
                'name'           => $fileName,
                'ext'            => strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                'size_formatted' => $fileSize > 0 ? self::formatFileSize($fileSize) : '',
                'local_path'     => $fileExists ? $localPath : null,
                'type'           => 'client',
                'file'           => $fileName,
                'ticket_code'    => $ticketCode,
            ];
        }

        return $clientFiles;
    }

    protected function resolveProgressDocument(array $p, string $ticketCode): array
    {
        $rawHtml = $p['progress'] ?? '';
        $allFiles = is_array($p['file'] ?? []) ? ($p['file'] ?? []) : [$p['file'] ?? ''];

        $videoExt = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv'];
        $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];

        $thumbnailFiles = [];
        $otherFiles     = [];
        $pdfFiles       = [];

        foreach ($allFiles as $file) {
            if (empty($file)) continue;

            $fileName = basename($file);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $localPath = storage_path("app/public/tickets/{$ticketCode}/{$fileName}");
            $fileExists = file_exists($localPath);
            $fileSize = $fileExists ? filesize($localPath) : 0;

            $encodedFileName = rawurlencode($fileName);
            $url = asset("storage/tickets/{$ticketCode}/{$encodedFileName}");

            $meta = [
                'file'           => $file,
                'url'            => $url,
                'name'           => $fileName,
                'ext'            => $ext,
                'size_bytes'     => $fileSize,
                'size_formatted' => $fileSize > 0 ? self::formatFileSize($fileSize) : '',
                'local_path'     => $fileExists ? $localPath : null,
                'type'           => 'internal',
            ];

            if ($ext === 'pdf') {
                $pdfFiles[] = $meta;
            } elseif (in_array($ext, $videoExt) || (in_array($ext, $imageExt) && $fileSize < 5_000_000)) {
                $thumbnailFiles[] = $meta;
            } else {
                $otherFiles[] = $meta;
            }
        }

        // Extract embedded images dari HTML
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $rawHtml, $matches);
        $embeddedImages = [];
        foreach ($matches[1] ?? [] as $url) {
            $fileName = basename(parse_url($url, PHP_URL_PATH));
            $fileName = rawurldecode($fileName);

            // 🔥 LANGSUNG PAKAI ticket_code untuk gambar
            $localPath = storage_path("app/public/tickets/{$ticketCode}/{$fileName}");
            if (file_exists($localPath)) {
                $embeddedImages[] = [
                    'url'  => $url,
                    'path' => $localPath,
                    'name' => basename($localPath),
                    'ext'  => strtolower(pathinfo($localPath, PATHINFO_EXTENSION)),
                ];
            }
        }

        return [
            'text'            => $rawHtml,
            'text_html'       => $rawHtml,
            'embedded_images' => $embeddedImages,
            'thumbnail_files' => $thumbnailFiles,
            'other_files'     => $otherFiles,
            'pdf_files'       => $pdfFiles,
            'timestamp'       => $p['timestamp'] ?? null,
        ];
    }

    public function resetFilters(): void
    {
        $this->form->fill($this->defaultFormState());
        $this->clearReport();

        Notification::make()->title('Filter Direset')
            ->body('Semua filter telah dikembalikan ke pengaturan awal.')
            ->info()->send();
    }

    protected function clearReport(): void
    {
        $this->hasReport     = false;
        $this->reportSummary = [];
        $this->debugInfo     = [];
        cache()->forget($this->cacheKey);
    }

    public function exportReport(): void
    {
        if (!$this->hasReport) {
            Notification::make()->title('Informasi')
                ->body('Tidak ada data untuk diexport. Tampilkan laporan terlebih dahulu.')
                ->warning()->send();
            return;
        }

        $reportData = cache()->get($this->cacheKey, []);

        if (empty($reportData)) {
            Notification::make()->title('Informasi')
                ->body('Sesi laporan telah habis. Silakan tampilkan laporan kembali.')
                ->warning()->send();
            return;
        }

        Log::info('Exporting report', [
            'cache_key' => $this->cacheKey,
            'summary' => $reportData['summary'] ?? null,
            'data_count' => count($reportData['data'] ?? [])
        ]);

        cache()->put($this->cacheKey . '_summary', $reportData['summary'] ?? [], now()->addMinutes(5));
        cache()->put($this->cacheKey . '_data',    $reportData['data'] ?? [],    now()->addMinutes(5));
        cache()->put($this->cacheKey . '_notes',   $this->data['admin_notes'] ?? '', now()->addMinutes(5));

        $this->dispatch('open-download-url', url: route('report.download', [
            'key' => $this->cacheKey,
        ]));
    }

    protected static function convertPdfWithGhostscript(string $inputPath, ?string $outputPath = null): ?string
    {
        if (!file_exists($inputPath)) {
            Log::error('PDF file not found for conversion', ['path' => $inputPath]);
            return null;
        }

        if (empty($outputPath)) {
            $outputPath = sys_get_temp_dir() . '/gs_converted_' . uniqid() . '.pdf';
        }

        $command = sprintf(
            'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT '
                . '-sDEVICE=pdfwrite '
                . '-dCompatibilityLevel=1.4 '
                . '-sOutputFile=%s %s 2>&1',
            escapeshellarg($outputPath),
            escapeshellarg($inputPath)
        );

        Log::info('Running Ghostscript conversion', [
            'input' => $inputPath,
            'output' => $outputPath,
        ]);

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
            Log::info('Ghostscript conversion successful', [
                'input' => $inputPath,
                'output' => $outputPath,
                'size' => filesize($outputPath)
            ]);
            return $outputPath;
        }

        Log::error('Ghostscript conversion failed', [
            'input' => $inputPath,
            'return_code' => $returnCode,
            'output' => $output
        ]);

        return null;
    }

    /**
     * Render PDF dengan merge lampiran
     */
    public static function renderPdfStatic(array $summary, array $data, ?string $notes): string
    {
        Log::info('=== RENDER PDF STATIC START ===');

        $attachedPdfs = [];

        foreach ($data as $ticket) {
            $ticketCode = $ticket['ticket_code'] ?? '';

            foreach ($ticket['documents'] ?? [] as $doc) {
                $isClientDoc = $doc['is_client_document'] ?? false;

                if ($isClientDoc) {
                    foreach ($doc['other_files'] ?? [] as $cf) {
                        if (strtolower($cf['ext'] ?? '') !== 'pdf') continue;

                        $localPath = $cf['local_path'] ?? null;

                        if (!$localPath || !file_exists($localPath)) {
                            $fileName = $cf['name'] ?? '';
                            if ($ticketCode && $fileName) {
                                $localPath = storage_path("app/public/tickets/{$ticketCode}/{$fileName}");
                            }
                        }

                        if ($localPath && file_exists($localPath)) {
                            $attachedPdfs[] = [
                                'name'           => $cf['name'] ?? basename($localPath),
                                'path'           => $localPath,
                                'url'            => $cf['url'] ?? '',
                                'ticket_code'    => $ticketCode,
                                'type'           => 'client_document',
                                'size_formatted' => $cf['size_formatted'] ?? '',
                            ];
                        }
                    }
                    continue;
                }

                foreach ($doc['pdf_files'] ?? [] as $pf) {
                    $localPath = $pf['local_path'] ?? null;

                    if (!$localPath || !file_exists($localPath)) {
                        $fileName = $pf['name'] ?? '';
                        if ($ticketCode && $fileName) {
                            $localPath = storage_path("app/public/tickets/{$ticketCode}/{$fileName}");
                        }
                    }

                    if ($localPath && file_exists($localPath)) {
                        $attachedPdfs[] = [
                            'name'           => $pf['name'] ?? basename($localPath),
                            'path'           => $localPath,
                            'url'            => $pf['url'] ?? asset("storage/tickets/{$ticketCode}/" . rawurlencode($pf['name'] ?? '')),
                            'ticket_code'    => $ticketCode,
                            'type'           => 'progress_document',
                            'size_formatted' => $pf['size_formatted'] ?? '',
                        ];
                    }
                }
            }
        }

        // Hapus duplikat
        $uniquePdfs = [];
        foreach ($attachedPdfs as $pdf) {
            $key = $pdf['path'];
            if (!isset($uniquePdfs[$key])) {
                $uniquePdfs[$key] = $pdf;
            }
        }
        $attachedPdfs = array_values($uniquePdfs);

        Log::info('Total unique PDFs to process', ['count' => count($attachedPdfs)]);

        // Konversi PDF dengan Ghostscript
        $convertedPdfPaths = [];
        foreach ($attachedPdfs as $index => $pdfData) {
            $convertedPath = self::convertPdfWithGhostscript($pdfData['path']);
            if ($convertedPath) {
                $convertedPdfPaths[] = $convertedPath;
                $attachedPdfs[$index]['converted_path'] = $convertedPath;
            } else {
                $attachedPdfs[$index]['converted_path'] = $pdfData['path'];
            }
        }

        // Generate HTML
        $html = view('filament.pages.pdf.report', compact('summary', 'data', 'notes', 'attachedPdfs'))->render();
        $html = self::replaceImageUrlsWithLocalPaths($html);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', false);

        $mainPdfPath = tempnam(sys_get_temp_dir(), 'report_main_') . '.pdf';
        file_put_contents($mainPdfPath, $pdf->output());

        // Cek ada PDF valid
        $hasValidPdfs = false;
        foreach ($attachedPdfs as $pdf) {
            if (file_exists($pdf['converted_path'])) {
                $hasValidPdfs = true;
                break;
            }
        }

        if (!$hasValidPdfs) {
            $output = file_get_contents($mainPdfPath);
            @unlink($mainPdfPath);
            return $output;
        }

        try {
            $fpdi = new Fpdi();

            $mainPageCount = $fpdi->setSourceFile($mainPdfPath);
            for ($i = 1; $i <= $mainPageCount; $i++) {
                $tpl = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'] ?? 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            foreach ($attachedPdfs as $pdfData) {
                $pdfPath = $pdfData['converted_path'];
                if (!file_exists($pdfPath)) continue;

                try {
                    $typeLabel = $pdfData['type'] === 'client_document' ? 'Dokumen Client' : 'Dokumen Progress';
                    $pageCount = $fpdi->setSourceFile($pdfPath);

                    for ($p = 1; $p <= $pageCount; $p++) {
                        $tpl = $fpdi->importPage($p);
                        $size = $fpdi->getTemplateSize($tpl);
                        $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                        $pageW = $orientation === 'L' ? 297 : 210;
                        $pageH = $orientation === 'L' ? 210 : 297;

                        $fpdi->AddPage($orientation);

                        // Header
                        $fpdi->SetFillColor(243, 244, 246);
                        $fpdi->Rect(0, 0, $pageW, 13, 'F');
                        $fpdi->SetDrawColor(209, 213, 219);
                        $fpdi->Line(0, 13, $pageW, 13);

                        $fpdi->SetFont('Helvetica', 'B', 7.5);
                        $fpdi->SetTextColor(31, 41, 55);
                        $fpdi->SetXY(10, 3.5);
                        $dispName = strlen($pdfData['name']) > 60 ? substr($pdfData['name'], 0, 57) . '...' : $pdfData['name'];
                        $fpdi->Cell(120, 4, mb_convert_encoding($dispName, 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');

                        $fpdi->SetFont('Helvetica', 'I', 6.5);
                        $fpdi->SetTextColor(107, 114, 128);
                        $fpdi->SetXY($pageW - 80, 3.5);
                        $fpdi->Cell(30, 4, $typeLabel, 0, 0, 'R');

                        $fpdi->SetFont('Helvetica', '', 6.5);
                        $fpdi->SetXY($pageW - 46, 3.5);
                        $fpdi->Cell(36, 4, $pdfData['ticket_code'] . '  ' . $p . '/' . $pageCount, 0, 0, 'R');

                        $margin = 8;
                        $headerH = 15;
                        $availW = $pageW - ($margin * 2);
                        $availH = $pageH - $headerH - $margin;
                        $scale = min($availW / $size['width'], $availH / $size['height'], 1.0);
                        $drawW = $size['width'] * $scale;
                        $drawH = $size['height'] * $scale;
                        $drawX = $margin + (($availW - $drawW) / 2);

                        $fpdi->useTemplate($tpl, $drawX, $headerH, $drawW, $drawH);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to merge PDF', ['name' => $pdfData['name'], 'error' => $e->getMessage()]);
                    self::addErrorPage($fpdi, $pdfData);
                }
            }

            $finalOutput = $fpdi->Output('S');

            @unlink($mainPdfPath);
            foreach ($convertedPdfPaths as $tempFile) {
                @unlink($tempFile);
            }

            return $finalOutput;
        } catch (\Exception $e) {
            Log::error('Fatal error during PDF merge', ['error' => $e->getMessage()]);
            $output = file_get_contents($mainPdfPath);
            @unlink($mainPdfPath);
            return $output;
        }
    }

    protected static function replaceImageUrlsWithLocalPaths(string $html): string
    {
        $ticketsDir = storage_path('app/public/tickets');

        return preg_replace_callback('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', function ($matches) use ($ticketsDir) {
            $url = $matches[1];
            $fileName = basename(parse_url($url, PHP_URL_PATH));
            $fileName = rawurldecode($fileName);

            if (is_dir($ticketsDir)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($ticketsDir, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getFilename() === $fileName) {
                        return str_replace($url, $file->getPathname(), $matches[0]);
                    }
                }
            }

            return $matches[0];
        }, $html);
    }

    protected static function addErrorPage($fpdi, array $att): void
    {
        $fpdi->AddPage();
        $fpdi->SetFont('Helvetica', 'B', 11);
        $fpdi->SetTextColor(185, 28, 28);
        $fpdi->SetY(120);
        $fpdi->Cell(0, 8, 'Gagal memuat lampiran:', 0, 1, 'C');
        $fpdi->SetFont('Helvetica', '', 9);
        $fpdi->SetTextColor(55, 65, 81);
        $fpdi->Cell(0, 6, mb_convert_encoding($att['name'] ?? '-', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    }

    protected function defaultFormState(): array
    {
        return [
            'selected_tickets' => null,
            'date_range'       => null,
            'start_date'       => null,
            'end_date'         => null,
            'client_name'      => null,
            'proposal_id'      => null,
            'enquiry'          => null,
            'proposal_for'     => null,
            'generated_by'     => null,
            'admin_notes'      => null,
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
            'total_tickets'   => $this->reportSummary['total_tickets'] ?? 0,
            'total_documents' => $totalDocs,
        ];
    }
}
