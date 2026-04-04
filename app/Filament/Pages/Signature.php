<?php

namespace App\Filament\Pages;

use App\Models\DocumentToss;
use App\Models\Ticket;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Imagick;
use Symfony\Component\Process\Process;

class Signature extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view            = 'filament.pages.signature';
    protected static ?string $title          = 'Signature';
    protected static ?string $navigationGroup = 'TOSS';

    public ?string $ticketId       = null;
    public ?string $selectedFile   = null;
    public ?string $pdfUrl         = null;
    public ?string $qrUrl          = null;
    public ?array  $data           = [];
    public array   $debugInfo      = [];
    public ?DocumentToss $currentDocument = null;

    // ── Access Control ─────────────────────────────────────────────────────────
    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_Signature');
    }

    // ── Mount ──────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->ticketId       = null;
        $this->selectedFile   = null;
        $this->currentDocument = null;
        $this->form->fill($this->emptyFormData());
        $this->addDebugInfo('Page mounted', [
            'tickets_count' => count($this->getTicketOptions()),
        ]);
    }

    // ── Form Definition ────────────────────────────────────────────────────────
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pilih File')
                    ->description('Pilih tiket dan file PDF yang akan diproses')
                    ->schema([
                        Select::make('ticketId')
                            ->label('Pilih Tiket')
                            ->placeholder('Pilih salah satu opsi')
                            ->options($this->getTicketOptions())
                            ->searchable()
                            ->live()
                            ->preload()
                            ->afterStateUpdated(function ($state) {
                                $this->ticketId       = $state;
                                $this->selectedFile   = null;
                                $this->currentDocument = null;
                                $this->clearPreview();
                                $this->form->fill(array_merge($this->emptyFormData(), ['ticketId' => $state]));
                                $this->addDebugInfo('Ticket selected', [
                                    'ticketUuid'     => $state,
                                    'availableFiles' => count($this->getFileOptions()),
                                ]);
                            }),

                        Select::make('selectedFile')
                            ->label('Pilih File PDF')
                            ->placeholder(fn() => $this->ticketId ? 'Pilih salah satu opsi' : 'Pilih ticket terlebih dahulu')
                            ->options(fn() => $this->getFileOptions())
                            ->disabled(fn() => ! $this->ticketId)
                            ->visible(fn() => $this->ticketId !== null)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if (! $this->ticketId) {
                                    $this->selectedFile = null;
                                    Notification::make()->title('Peringatan')->body('Pilih ticket terlebih dahulu.')->warning()->send();
                                    return;
                                }
                                $this->selectedFile = $state;
                                $this->loadExistingDocument();
                                if ($state) {
                                    $this->generatePreview();
                                } else {
                                    $this->clearPreview();
                                    $this->currentDocument = null;
                                }
                            }),

                        Actions::make([
                            Action::make('generatePreview')
                                ->label('🔄 Generate Preview')
                                ->color('primary')
                                ->action('generatePreview')
                                ->visible(fn(): bool => ! empty($this->selectedFile) && ! empty($this->ticketId)),
                            Action::make('clearPreview')
                                ->label('🗑️ Clear Preview')
                                ->color('gray')
                                ->action('clearPreview')
                                ->visible(fn(): bool => ! empty($this->pdfUrl)),
                        ]),
                    ]),

                Section::make('Informasi Dokumen')
                    ->description('Isi informasi detail dokumen')
                    ->visible(fn() => ! empty($this->selectedFile))
                    ->schema([
                        TextInput::make('document_name')
                            ->label('Nama Dokumen')
                            ->placeholder('Masukkan nama dokumen')
                            ->columnSpan(2)
                            ->required()
                            ->maxLength(255),

                        Textarea::make('document_description')
                            ->label('Deskripsi Dokumen')
                            ->placeholder('Masukkan deskripsi dokumen')
                            ->columnSpan(2)
                            ->required()
                            ->rows(3),

                        TextInput::make('document_bySign')
                            ->label('Penandatangan')
                            ->placeholder('Nama penandatangan dokumen')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('document_toReceive')
                            ->label('Penerima Dokumen')
                            ->placeholder('Nama penerima dokumen')
                            ->required()
                            ->maxLength(255),

                        Select::make('document_action')
                            ->label('Tindakan Dokumen')
                            ->placeholder('Pilih tindakan dokumen')
                            ->options(['Tanda Tangan' => 'Tanda Tangan'])
                            ->required(),

                        TextInput::make('document_no')
                            ->label('Nomor Dokumen')
                            ->placeholder('Nomor dokumen')
                            ->required()
                            ->maxLength(100),

                        DateTimePicker::make('document_start')
                            ->label('Tanggal Mulai Aktif Dokumen')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->default(now()->format('d/m/Y')),

                        DateTimePicker::make('document_end')
                            ->label('Tanggal Berakhir Dokumen')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->default(null),

                        RichEditor::make('document_notes')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'strike',
                                'underline',
                            ])
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    // ── Load Existing Document ─────────────────────────────────────────────────
    protected function loadExistingDocument(): void
    {
        if (! $this->ticketId || ! $this->selectedFile) {
            return;
        }

        try {
            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) return;

            $this->currentDocument = DocumentToss::where('ticket_code', $ticket->ticket_code)
                ->where('document_path', $this->selectedFile)
                ->first();

            if ($this->currentDocument) {
                $this->form->fill([
                    'ticketId'             => $this->ticketId,
                    'selectedFile'         => $this->selectedFile,
                    'document_name'        => $this->currentDocument->document_name        ?? '',
                    'document_description' => $this->currentDocument->document_description ?? '',
                    'document_bySign'      => $this->currentDocument->document_bySign      ?? '',
                    'document_toReceive'   => $this->currentDocument->document_toReceive   ?? '',
                    'document_action'      => $this->currentDocument->document_action      ?? '',
                    'document_no'          => $this->currentDocument->document_no          ?? '',
                    'document_start'       => $this->currentDocument->document_start       ?? null,
                    'document_end'         => $this->currentDocument->document_end         ?? null,
                    'document_notes'       => $this->currentDocument->document_notes       ?? '',
                ]);
                Notification::make()->title('Dokumen Ditemukan')->body('Data dokumen yang sudah ada telah dimuat.')->info()->send();
            }
        } catch (\Exception $e) {
            $this->addDebugInfo('Error loading existing document', ['error' => $e->getMessage()]);
        }
    }

    // ── Ticket Options ─────────────────────────────────────────────────────────
    protected function getTicketOptions(): array
    {
        try {
            $tickets = Ticket::select('uuid', 'ticket_code', 'ticket_title')
                ->whereNotNull('uuid')->whereNotNull('ticket_code')
                ->where('uuid', '!=', '')->where('ticket_code', '!=', '')
                ->orderBy('created_at', 'desc')
                ->get();

            $options = [];
            foreach ($tickets as $ticket) {
                if (! empty($ticket->uuid) && ! empty($ticket->ticket_code)) {
                    $options[$ticket->uuid] = $ticket->ticket_code . ' - ' . $ticket->ticket_title;
                }
            }

            $this->addDebugInfo('Tickets loaded', [
                'total'   => $tickets->count(),
                'options' => count($options),
            ]);

            return $options;
        } catch (\Exception $e) {
            Log::error('Error loading tickets', ['error' => $e->getMessage()]);
            Notification::make()->title('Error')->body('Gagal memuat daftar tiket: ' . $e->getMessage())->danger()->send();
            return [];
        }
    }

    // ── File Options ───────────────────────────────────────────────────────────
    protected function getFileOptions(): array
    {
        try {
            if (! $this->ticketId) return [];

            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) return [];

            $files        = [];
            $ticketFolder = "tickets/{$ticket->ticket_code}";

            if (Storage::disk('public')->exists($ticketFolder)) {
                foreach (Storage::disk('public')->files($ticketFolder) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $files[$file] = basename($file);
                    }
                }
            }

            $this->addDebugInfo('Files loaded', ['count' => count($files)]);
            return $files;
        } catch (\Exception $e) {
            Notification::make()->title('Error')->body('Gagal memuat daftar file: ' . $e->getMessage())->danger()->send();
            return [];
        }
    }

    // ── Generate Preview ───────────────────────────────────────────────────────
    public function generatePreview(): void
    {
        try {
            if (! $this->ticketId) {
                Notification::make()->title('Warning')->body('Pilih ticket terlebih dahulu.')->warning()->send();
                return;
            }
            if (! $this->selectedFile) {
                Notification::make()->title('Warning')->body('Pilih file PDF terlebih dahulu.')->warning()->send();
                return;
            }

            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) throw new \Exception('Ticket tidak ditemukan');

            if (! Storage::disk('public')->exists($this->selectedFile)) {
                throw new \Exception('File PDF tidak ditemukan: ' . $this->selectedFile);
            }

            $fileName      = pathinfo($this->selectedFile, PATHINFO_FILENAME);
            $progressIndex = $this->extractProgressIndex($fileName);
            $cleanFileName = $this->sanitizeFileName($fileName);
            $slug          = "{$ticket->ticket_code}_progress{$progressIndex}_{$cleanFileName}";
            $qrPath        = "qrcodes/{$slug}.png";

            Storage::disk('public')->makeDirectory('qrcodes');

            $qrContent = url('toss/' . $slug);
            Storage::disk('public')->put($qrPath, QrCode::format('png')->size(300)->generate($qrContent));

            $this->pdfUrl = Storage::disk('public')->url($this->selectedFile);
            $this->qrUrl  = Storage::disk('public')->url($qrPath);

            $this->addDebugInfo('Preview generated', [
                'slug'      => $slug,
                'qrContent' => $qrContent,
                'pdfUrl'    => $this->pdfUrl,
                'qrUrl'     => $this->qrUrl,
            ]);

            $this->dispatch('preview-updated', ['pdfUrl' => $this->pdfUrl, 'qrUrl' => $this->qrUrl]);
            Notification::make()->title('Success')->body('Preview berhasil di-generate!')->success()->send();
        } catch (\Exception $e) {
            Log::error('Error generate preview', ['error' => $e->getMessage()]);
            Notification::make()->title('Error')->body('Gagal generate preview: ' . $e->getMessage())->danger()->send();
        }
    }

    // ── Clear Preview ──────────────────────────────────────────────────────────
    public function clearPreview(): void
    {
        $this->pdfUrl = null;
        $this->qrUrl  = null;
        $this->addDebugInfo('Preview cleared', []);
        $this->dispatch('preview-cleared');
    }

    // ── Save QR Position (main, called from JS via Livewire event) ─────────────
    #[On('save-qr-position')]
    public function savePosition(float $x, float $y, float $scale, int $page = 1): void
    {
        try {
            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) throw new \Exception('Ticket tidak ditemukan');

            $fileName      = pathinfo($this->selectedFile, PATHINFO_FILENAME);
            $progressIndex = $this->extractProgressIndex($fileName);
            $cleanFileName = $this->sanitizeFileName($fileName);
            $slug          = "{$ticket->ticket_code}_progress{$progressIndex}_{$cleanFileName}";

            $qrPath      = Storage::disk('public')->path("qrcodes/{$slug}.png");
            $pdfPath     = Storage::disk('public')->path($this->selectedFile);
            $outputDir   = Storage::disk('public')->path('Tosses');
            $finalOutput = "{$outputDir}/{$slug}_final.pdf";

            Storage::disk('public')->makeDirectory('Tosses');

            // ── 1. Render halaman yang dipilih user ke PNG ─────────────────────
            $imagick = new Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage("{$pdfPath}[" . ($page - 1) . "]"); // 0-based index
            $imagick->setImageFormat('png');

            $pageImg = "{$outputDir}/{$slug}_page{$page}.png";
            $imagick->writeImage($pageImg);
            $imagick->clear();
            $imagick->destroy();

            // ── 2. Tempel QR ke halaman PNG ────────────────────────────────────
            // Koordinat dalam sistem PDF (origin kiri-bawah) → konversi ke pixel
            $canvasWidth  = 892;
            $canvasHeight = 1262;

            $img = new Imagick($pageImg);
            $qr  = new Imagick($qrPath);
            $qr->resizeImage(150, 150, Imagick::FILTER_LANCZOS, 1);

            $imgWidth  = $img->getImageWidth();
            $imgHeight = $img->getImageHeight();
            $xPx = (int) ($x / $canvasWidth  * $imgWidth);
            $yPx = (int) (($canvasHeight - $y) / $canvasHeight * $imgHeight);

            $img->compositeImage($qr, Imagick::COMPOSITE_OVER, $xPx, $yPx);
            $mergedPage = "{$outputDir}/{$slug}_merged_page{$page}.png";
            $img->writeImage($mergedPage);

            // ── 3. Convert merged PNG → PDF ────────────────────────────────────
            $mergedPdf = "{$outputDir}/{$slug}_page{$page}.pdf";
            $img->setImageFormat('pdf');
            $img->writeImage($mergedPdf);
            $img->clear();
            $img->destroy();
            $qr->clear();
            $qr->destroy();

            // ── 4. Extract halaman sebelum & sesudah dari PDF asli ─────────────
            $escapedPdfPath  = escapeshellarg($pdfPath);
            $escapedMergedPdf = escapeshellarg($mergedPdf);
            $escapedFinal    = escapeshellarg($finalOutput);
            $parts           = [];

            // Halaman 1 s/d (page-1)
            if ($page > 1) {
                $beforePdf = "{$outputDir}/{$slug}_before.pdf";
                $escapedBefore = escapeshellarg($beforePdf);
                Process::fromShellCommandline(
                    "gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER " .
                        "-dFirstPage=1 -dLastPage=" . ($page - 1) . " " .
                        "-sOutputFile={$escapedBefore} {$escapedPdfPath}"
                )->run();
                $parts[] = $escapedBefore;
            }

            // Halaman yang sudah ditempeli QR
            $parts[] = $escapedMergedPdf;

            // Halaman (page+1) s/d akhir
            $afterPdf = "{$outputDir}/{$slug}_after.pdf";
            $escapedAfter = escapeshellarg($afterPdf);
            $extractAfter = Process::fromShellCommandline(
                "gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER " .
                    "-dFirstPage=" . ($page + 1) . " " .
                    "-sOutputFile={$escapedAfter} {$escapedPdfPath}"
            );
            $extractAfter->run();
            // Hanya tambahkan jika berhasil (ada halaman sesudahnya)
            if ($extractAfter->isSuccessful() && file_exists($afterPdf) && filesize($afterPdf) > 0) {
                $parts[] = $escapedAfter;
            }

            // ── 5. Gabungkan semua bagian jadi satu PDF final ──────────────────
            $partsList = implode(' ', $parts);
            Process::fromShellCommandline(
                "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$escapedFinal} {$partsList}"
            )->run();

            // ── 6. Hapus file sementara ────────────────────────────────────────
            foreach ([$pageImg, $mergedPage, $mergedPdf, $afterPdf] as $f) {
                if (isset($f) && file_exists($f)) unlink($f);
            }
            if ($page > 1 && isset($beforePdf) && file_exists($beforePdf)) {
                unlink($beforePdf);
            }

            // ── 7. Simpan ke database ──────────────────────────────────────────
            $attributes = [
                'ticket_code'   => $ticket->ticket_code,
                'document_path' => $this->selectedFile,
            ];

            $values = [
                'qr_position_x'        => $x,
                'qr_position_y'        => $y,
                'qr_scale'             => $scale,
                'scale'                => $scale,
                'qr_page'              => $page,
                'qr_path'              => "qrcodes/{$slug}.png",
                'document_name'        => $this->data['document_name']        ?? '',
                'document_description' => $this->data['document_description'] ?? '',
                'document_bySign'      => $this->data['document_bySign']      ?? '',
                'document_toReceive'   => $this->data['document_toReceive']   ?? '',
                'document_action'      => $this->data['document_action']      ?? '',
                'document_no'          => $this->data['document_no']          ?? '',
                'document_notes'       => $this->data['document_notes']       ?? '',
                'document_final_path'  => "Tosses/{$slug}_final.pdf",
                'document_slug'        => $slug,
                'document_start'       => $this->data['document_start']       ?? null,
                'document_end'         => $this->data['document_end']         ?? null,
            ];

            DocumentToss::updateOrCreate($attributes, $values);
            Notification::make()->title('Data Berhasil Disimpan')->body("QR ditempel di halaman {$page}.")->success()->send();
        } catch (\Exception $e) {
            Log::error('Error savePosition', ['error' => $e->getMessage()]);
            Notification::make()->title('Error')->body('Data gagal disimpan: ' . $e->getMessage())->danger()->send();
        }
    }

    // ── Alias for direct JS calls ──────────────────────────────────────────────
    public function saveQrPosition(float $x, float $y, float $scale, int $page = 1): void
    {
        $this->savePosition($x, $y, $scale, $page);
    }

    // ── Debug ──────────────────────────────────────────────────────────────────
    #[On('refresh-debug')]
    public function refreshDebug(): void
    {
        $this->addDebugInfo('Debug refreshed', ['timestamp' => now()->toDateTimeString()]);
    }

    public function refreshTickets(): void
    {
        $this->ticketId       = null;
        $this->selectedFile   = null;
        $this->currentDocument = null;
        $this->form->fill($this->emptyFormData());
        $this->dispatch('$refresh');
        Notification::make()->title('Refreshed')->body('Daftar tiket diperbarui.')->info()->send();
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    private function emptyFormData(): array
    {
        return [
            'ticketId'             => null,
            'selectedFile'         => null,
            'document_name'        => '',
            'document_description' => '',
            'document_bySign'      => '',
            'document_toReceive'   => '',
            'document_action'      => '',
            'document_no'          => '',
            'document_start'       => null,
            'document_end'         => null,
            'document_notes'       => '',
        ];
    }

    private function extractProgressIndex(string $fileName): int
    {
        if (preg_match('/progress-(\d+)/', $fileName, $matches)) {
            return (int) $matches[1];
        }
        return 1;
    }

    private function sanitizeFileName(string $fileName): string
    {
        $clean = str_replace(' ', '_', $fileName);
        $clean = preg_replace('/[^\w\-\.]/', '_', $clean);
        $clean = preg_replace('/_+/', '_', $clean);
        return trim($clean, '_');
    }

    private function addDebugInfo(string $action, array $data): void
    {
        $this->debugInfo[] = [
            'time'   => now()->format('H:i:s.u'),
            'action' => $action,
            'data'   => $data,
        ];
        if (count($this->debugInfo) > 15) {
            $this->debugInfo = array_slice($this->debugInfo, -15);
        }
    }

    // ── Computed Properties ────────────────────────────────────────────────────
    #[Computed] public function hasPdf(): bool
    {
        return ! empty($this->pdfUrl);
    }
    #[Computed] public function hasQr(): bool
    {
        return ! empty($this->qrUrl);
    }
    #[Computed] public function canPreview(): bool
    {
        return $this->hasPdf && $this->hasQr;
    }
    #[Computed] public function isTicketSelected(): bool
    {
        return ! empty($this->ticketId);
    }
    #[Computed] public function hasCurrentDocument(): bool
    {
        return $this->currentDocument !== null;
    }
    #[Computed] public function canSaveDocument(): bool
    {
        return ! empty($this->selectedFile) && ! empty($this->ticketId);
    }
    #[Computed] public function availableFilesCount(): int
    {
        return count($this->getFileOptions());
    }
}
