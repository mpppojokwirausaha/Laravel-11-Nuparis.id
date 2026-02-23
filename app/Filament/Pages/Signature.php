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
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Imagick;
use Symfony\Component\Process\Process;

class Signature extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.signature';
    protected static ?string $title = 'Signature';

    public ?string $ticketId = null; // UUID
    public ?string $selectedFile = null;
    public ?string $pdfUrl = null;
    public ?string $qrUrl = null;
    public ?array $data = [];
    public array $debugInfo = [];
    public ?DocumentToss $currentDocument = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_Signature');
    }
    public function mount(): void
    {
        $this->ticketId = null;
        $this->selectedFile = null;
        $this->currentDocument = null;
        $this->form->fill([
            'ticketId' => null,
            'selectedFile' => null,
            'document_name' => '',
            'document_description' => '',
            'document_signer' => '',
            'document_recipient' => '',
            'document_action' => '',
            'document_number' => '',
            'notes' => ''
        ]);
        $this->addDebugInfo('Page mounted', [
            'tickets_count' => count($this->getTicketOptions())
        ]);
    }

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
                            ->searchable() // Add searchable for better UX
                            ->live()
                            ->preload() // Add preload to ensure options are loaded
                            ->afterStateUpdated(function ($state) {
                                $this->addDebugInfo('Ticket selection changed', [
                                    'oldTicket' => $this->ticketId,
                                    'newTicket' => $state
                                ]);
                                
                                // Reset file selection when ticket changes
                                $this->ticketId = $state;
                                $this->selectedFile = null;
                                $this->currentDocument = null;
                                $this->clearPreview();
                                
                                // Update form state to reset file dropdown and document fields
                                $this->form->fill([
                                    'ticketId' => $state,
                                    'selectedFile' => null,
                                    'document_name' => '',
                                    'document_description' => '',
                                    'document_signer' => '',
                                    'document_recipient' => '',
                                    'document_action' => '',
                                    'document_number' => '',
                                    'notes' => ''
                                ]);
                                
                                $this->addDebugInfo('Ticket selected', [
                                    'ticketUuid' => $state,
                                    'availableFiles' => count($this->getFileOptions())
                                ]);
                            }),

                        Select::make('selectedFile')
                            ->label('Pilih File PDF')
                            ->placeholder(function () {
                                return $this->ticketId 
                                    ? 'Pilih salah satu opsi' 
                                    : 'Pilih ticket terlebih dahulu';
                            })
                            ->options(fn () => $this->getFileOptions())
                            ->disabled(fn () => !$this->ticketId)
                            ->visible(fn () => $this->ticketId !== null)
                            ->searchable() // Add searchable for better UX
                            ->live()
                            ->afterStateUpdated(function (string $operation, $state) {
                                // Validate that ticket is selected
                                if (!$this->ticketId) {
                                    $this->selectedFile = null;
                                    $this->addDebugInfo('File selection blocked - no ticket', []);
                                    
                                    Notification::make()
                                        ->title('Peringatan')
                                        ->body('Pilih ticket terlebih dahulu sebelum memilih file.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                $this->selectedFile = $state;
                                $this->addDebugInfo('File selected', [
                                    'operation' => $operation,
                                    'file' => $state,
                                    'ticketId' => $this->ticketId
                                ]);

                                // Check if document exists for this file
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
                                ->visible(fn (): bool => !empty($this->selectedFile) && !empty($this->ticketId)),
                            Action::make('clearPreview')
                                ->label('🗑️ Clear Preview')
                                ->color('gray')
                                ->action('clearPreview')
                                ->visible(fn (): bool => !empty($this->pdfUrl))
                        ])
                    ]),

                Section::make('Informasi Dokumen')
                    ->description('Isi informasi detail dokumen')
                    ->visible(fn () => !empty($this->selectedFile))
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
                            ->options([
                                'Tanda Tangan' => 'Tanda Tangan',
                            ])
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
                            ->default(now()->format('d/m/Y'))
                            ->extraAttributes([
                                'data-flatpickr' => json_encode([
                                    'enableTime' => false,
                                    'dateFormat' => 'd/m/Y',
                                ]),
                            ]),

                        DateTimePicker::make('document_end')
                            ->label('Tanggal Mulai Aktif Dokumen')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->default(null)
                            ->extraAttributes([
                                'data-flatpickr' => json_encode([
                                    'enableTime' => false,
                                    'dateFormat' => 'd/m/Y',
                                ]),
                            ]),

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
                            ])->columnSpanFull()
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function loadExistingDocument(): void
    {
        if (!$this->ticketId || !$this->selectedFile) {
            return;
        }

        try {
            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (!$ticket) {
                return;
            }

            // Look for existing document with this file path
            $this->currentDocument = DocumentToss::where('ticket_id', $ticket->id)
                ->where('pdf_path', $this->selectedFile)
                ->first();

            if ($this->currentDocument) {
                // Fill form with existing data
                $this->form->fill([
                    'ticketId' => $this->ticketId,
                    'selectedFile' => $this->selectedFile,
                    'document_name' => $this->currentDocument->nama_document ?? '',
                    'document_description' => $this->currentDocument->description_document ?? '',
                    'document_signer' => $this->currentDocument->penandatangan_document ?? '',
                    'document_recipient' => $this->currentDocument->penerima_document ?? '',
                    'document_action' => $this->currentDocument->tindakan_document ?? '',
                    'document_number' => $this->currentDocument->no_document ?? '',
                    'notes' => $this->currentDocument->catatan ?? ''
                ]);

                $this->addDebugInfo('Existing document loaded', [
                    'document_id' => $this->currentDocument->id,
                    'document_name' => $this->currentDocument->nama_document
                ]);

                Notification::make()
                    ->title('Dokumen Ditemukan')
                    ->body('Data dokumen yang sudah ada telah dimuat.')
                    ->info()
                    ->send();
            }
        } catch (\Exception $e) {
            $this->addDebugInfo('Error loading existing document', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getTicketOptions(): array
    {
        try {
            Log::info('Getting ticket options...');
            
            // More robust query with better error handling
            $tickets = Ticket::select('uuid', 'ticket_code')
                ->whereNotNull('uuid')
                ->whereNotNull('ticket_code')
                ->where('uuid', '!=', '')
                ->where('ticket_code', '!=', '')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Raw tickets from database:', [
                'count' => $tickets->count(),
                'sample' => $tickets->take(3)->toArray()
            ]);

            $options = [];
            
            foreach ($tickets as $ticket) {
                // Ensure both uuid and ticket_code are valid
                if (!empty($ticket->uuid) && !empty($ticket->ticket_code)) {
                    $options[$ticket->uuid] = $ticket->ticket_code;
                }
            }

            Log::info('Processed ticket options:', [
                'options_count' => count($options),
                'options' => $options
            ]);

            $this->addDebugInfo('Tickets loaded', [
                'total_from_db' => $tickets->count(),
                'valid_options' => count($options),
                'sample_tickets' => $tickets->take(3)->pluck('ticket_code', 'uuid')->toArray(),
                'final_options' => $options
            ]);

            if (empty($options)) {
                $this->addDebugInfo('No valid tickets found', [
                    'total_tickets_in_db' => Ticket::count(),
                    'tickets_with_uuid' => Ticket::whereNotNull('uuid')->where('uuid', '!=', '')->count(),
                    'tickets_with_code' => Ticket::whereNotNull('ticket_code')->where('ticket_code', '!=', '')->count()
                ]);

                // Try to get any tickets at all
                $anyTickets = Ticket::select('uuid', 'ticket_code')->limit(5)->get();
                Log::info('Sample of any tickets in database:', [
                    'tickets' => $anyTickets->toArray()
                ]);
            }

            return $options;

        } catch (\Exception $e) {
            $this->addDebugInfo('Error loading tickets', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Log::error('Error loading tickets in Toss page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Show notification to user
            Notification::make()
                ->title('Error')
                ->body('Gagal memuat daftar tiket: ' . $e->getMessage())
                ->danger()
                ->send();

            return [];
        }
    }

    protected function getFileOptions(): array
    {
        try {
            // Return empty if no ticket selected
            if (!$this->ticketId) {
                $this->addDebugInfo('No ticket ID for file options', []);
                return [];
            }

            $ticket = Ticket::where('uuid', $this->ticketId)->first();

            if (!$ticket) {
                $this->addDebugInfo('Ticket not found', ['ticketUuid' => $this->ticketId]);
                return [];
            }

            $files = [];
            $ticketFolder = "tickets/{$ticket->ticket_code}";

            if (Storage::disk('public')->exists($ticketFolder)) {
                $allFiles = Storage::disk('public')->files($ticketFolder);

                foreach ($allFiles as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $fileName = basename($file);
                        $files[$file] = $fileName;
                    }
                }
            }

            $this->addDebugInfo('Files loaded', [
                'ticketCode' => $ticket->ticket_code,
                'folder' => $ticketFolder,
                'folder_exists' => Storage::disk('public')->exists($ticketFolder),
                'count' => count($files),
                'files' => array_values($files)
            ]);

            return $files;

        } catch (\Exception $e) {
            $this->addDebugInfo('Error loading files', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error')
                ->body('Gagal memuat daftar file: ' . $e->getMessage())
                ->danger()
                ->send();

            return [];
        }
    }

    public function generatePreview(): void
    {
        try {
            // Double check validation
            if (!$this->ticketId) {
                Notification::make()
                    ->title('Warning')
                    ->body('Pilih ticket terlebih dahulu.')
                    ->warning()
                    ->send();
                return;
            }

            if (!$this->selectedFile) {
                Notification::make()
                    ->title('Warning')
                    ->body('Pilih file PDF terlebih dahulu.')
                    ->warning()
                    ->send();
                return;
            }

            Log::info("=== Generate Preview Called ===", [
                'ticket_uuid' => $this->ticketId,
                'file' => $this->selectedFile
            ]);

            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (!$ticket) {
                throw new \Exception('Ticket tidak ditemukan');
            }

            if (!Storage::disk('public')->exists($this->selectedFile)) {
                throw new \Exception('File PDF tidak ditemukan: ' . $this->selectedFile);
            }

            $fileName = pathinfo($this->selectedFile, PATHINFO_FILENAME);
            $progressIndex = $this->extractProgressIndex($fileName);
            
            // Ganti spasi dengan underscore pada slug
            $cleanFileName = $this->sanitizeFileName($fileName);
            $slug = "{$ticket->ticket_code}_progress{$progressIndex}_{$cleanFileName}";
            $qrPath = "qrcodes/{$slug}.png";

            Storage::disk('public')->makeDirectory('qrcodes');

            $qrContent = url('toss/'. $slug);

            $qrImage = QrCode::format('png')->size(300)->generate($qrContent);
            Storage::disk('public')->put($qrPath, $qrImage);

            $this->pdfUrl = Storage::disk('public')->url($this->selectedFile);
            $this->qrUrl = Storage::disk('public')->url($qrPath);

            Log::info("Preview generated", [
                'pdfUrl' => $this->pdfUrl,
                'qrUrl' => $this->qrUrl,
                'qrContent' => $qrContent
            ]);

            $this->addDebugInfo('Preview generated', [
                'fileName' => $fileName,
                'cleanFileName' => $cleanFileName,
                'progressIndex' => $progressIndex,
                'slug' => $slug,
                'qrContent' => $qrContent,
                'pdfUrl' => $this->pdfUrl,
                'qrUrl' => $this->qrUrl,
                'qrPath' => $qrPath
            ]);

            $this->dispatch('preview-updated', [
                'pdfUrl' => $this->pdfUrl,
                'qrUrl' => $this->qrUrl
            ]);

            Notification::make()
                ->title('Success')
                ->body('Preview berhasil di-generate!')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error("Error generate preview", [
                'error' => $e->getMessage()
            ]);

            $this->addDebugInfo('Error generating preview', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error')
                ->body('Gagal generate preview: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearPreview(): void
    {
        $this->pdfUrl = null;
        $this->qrUrl = null;

        $this->addDebugInfo('Preview cleared', []);
        
        // Only dispatch clear event, don't show notification automatically
        $this->dispatch('preview-cleared');
    }

    #[On('save-qr-position')]
    public function savePosition(float $x, float $y, float $scale): void
    {
        $ticket = Ticket::where('uuid', $this->ticketId)->first();

        $fileName = pathinfo($this->selectedFile, PATHINFO_FILENAME);
        $progressIndex = $this->extractProgressIndex($fileName);
        
        // Ganti spasi dengan underscore pada slug
        $cleanFileName = $this->sanitizeFileName($fileName);
        $slug = "{$ticket->ticket_code}_progress{$progressIndex}_{$cleanFileName}";

        // Gunakan cleanFileName untuk path
        $qrPath = Storage::disk('public')->path("qrcodes/{$slug}.png");
        $pdfPath = Storage::disk('public')->path($this->selectedFile);
        $outputDir = Storage::disk('public')->path('Tosses');
        $finalOutput = "{$outputDir}/{$slug}_final.pdf";

        Storage::disk('public')->makeDirectory('Tosses');

        // 1. Ambil halaman pertama sebagai gambar
        $imagick = new Imagick();
        $imagick->setResolution(150, 150); // kualitas
        $imagick->readImage("{$pdfPath}[0]"); // hanya halaman pertama
        $imagick->setImageFormat("png");

        $firstPageImg = "{$outputDir}/{$slug}_page1.png";
        $imagick->writeImage($firstPageImg);

        // 2. Tempel QR di posisi yang dihitung
        $canvasWidth = 892;
        $canvasHeight = 1262;

        $img = new Imagick($firstPageImg);
        $qr = new Imagick($qrPath);
        $qr->resizeImage(150, 150, Imagick::FILTER_LANCZOS, 1); // 20mm kira-kira 150px @150dpi

        // Hitung posisi
        $imgWidth = $img->getImageWidth();
        $imgHeight = $img->getImageHeight();
        $xPx = (int) ($x / $canvasWidth * $imgWidth);
        $yPx = (int) (($canvasHeight - $y) / $canvasHeight * $imgHeight); // flip y

        $img->compositeImage($qr, Imagick::COMPOSITE_OVER, $xPx, $yPx);
        $mergedPage = "{$outputDir}/{$slug}_merged_page1.png";
        $img->writeImage($mergedPage);

        // 3. Convert hasil ke PDF
        $mergedPdf = "{$outputDir}/{$slug}_page1.pdf";
        $img->setImageFormat("pdf");
        $img->writeImage($mergedPdf);

        // 4. Extract sisa halaman PDF (halaman 2 dst)
        $remainingPdf = "{$outputDir}/{$slug}_remaining.pdf";
        
        // Gunakan escaped paths untuk command line
        $escapedPdfPath = escapeshellarg($pdfPath);
        $escapedRemainingPdf = escapeshellarg($remainingPdf);
        
        $processExtract = Process::fromShellCommandline("gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER -dFirstPage=2 -sOutputFile={$escapedRemainingPdf} {$escapedPdfPath}");
        $processExtract->run();

        // 5. Gabungkan hasil halaman 1 + sisa halaman
        $escapedMergedPdf = escapeshellarg($mergedPdf);
        $escapedFinalOutput = escapeshellarg($finalOutput);
        
        $processMerge = Process::fromShellCommandline("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$escapedFinalOutput} {$escapedMergedPdf} {$escapedRemainingPdf}");
        $processMerge->run();

        // Optional: hapus sementara
        if (file_exists($firstPageImg)) unlink($firstPageImg);
        if (file_exists($mergedPage)) unlink($mergedPage);
        if (file_exists($mergedPdf)) unlink($mergedPdf);
        if (file_exists($remainingPdf)) unlink($remainingPdf);

        $attributes = [
            'ticket_code' => $ticket->ticket_code,
            'document_path' => $this->selectedFile,
        ];

        $values = [
            'qr_position_x' => $x,
            'qr_position_y' => $y,
            'qr_scale' => $scale,
            'scale' => $scale,
            'qr_path' => "qrcodes/{$slug}.png",
            'document_name' => $this->data['document_name'],
            'document_description' => $this->data['document_description'],
            'document_bySign' => $this->data['document_bySign'],
            'document_toReceive' => $this->data['document_toReceive'],
            'document_action' => $this->data['document_action'],
            'document_no' => $this->data['document_no'],
            'document_notes' => $this->data['document_notes'],
            'document_final_path' => "Tosses/{$slug}_final.pdf",
            'document_slug' => $slug,
            'document_start' => $this->data['document_start'],
            'document_end' => $this->data['document_end'],
        ];

        try {
            DocumentToss::updateOrCreate($attributes, $values);
            Notification::make()
                ->title('Data Berhasil Disimpan')
                ->body('Data berhasil disimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Data gagal disimpan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function savePosition01(float $x, float $y, float $scale): void
    {
        try {
            Log::info("=== Save QR Position ===", [
                'x' => $x,
                'y' => $y,
                'scale' => $scale
            ]);

            // Save position to current document if exists
            if ($this->currentDocument) {
                $this->currentDocument->update([
                    'qr_position_x' => $x,
                    'qr_position_y' => $y,
                    'qr_scale' => $scale
                ]);

                $this->addDebugInfo('QR Position saved to database', [
                    'document_id' => $this->currentDocument->id,
                    'x' => $x,
                    'y' => $y,
                    'scale' => $scale
                ]);

                Notification::make()
                    ->title('Position Saved')
                    ->body("QR position saved to database: X={$x}, Y={$y}, Scale={$scale}")
                    ->success()
                    ->send();
            } else {
                $this->addDebugInfo('QR Position saved (temporary)', [
                    'x' => $x,
                    'y' => $y,
                    'scale' => $scale,
                    'note' => 'Document not saved yet'
                ]);

                Notification::make()
                    ->title('Position Saved')
                    ->body("QR position saved temporarily. Save document to persist.")
                    ->info()
                    ->send();
            }

            $this->dispatch('position-saved', [
                'x' => $x,
                'y' => $y,
                'scale' => $scale
            ]);

        } catch (\Exception $e) {
            Log::error("Error save QR position", ['error' => $e->getMessage()]);

            $this->addDebugInfo('Error saving position', [
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Error')
                ->body('Gagal menyimpan posisi: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    // Alternative method for direct calls from JavaScript
    public function saveQrPosition(float $x, float $y, float $scale): void
    {
        $this->savePosition($x, $y, $scale);
    }

    #[On('refresh-debug')]
    public function refreshDebug(): void
    {
        $this->addDebugInfo('Debug refreshed', [
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function extractProgressIndex(string $fileName): int
    {
        if (preg_match('/progress-(\d+)/', $fileName, $matches)) {
            return (int) $matches[1];
        }
        return 1;
    }

    /**
     * Sanitize file name - replace spaces and special characters with underscores
     */
    private function sanitizeFileName(string $fileName): string
    {
        // Ganti spasi dengan underscore
        $cleanName = str_replace(' ', '_', $fileName);
        
        // Hapus karakter khusus lainnya (opsional)
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $cleanName);
        
        // Hapus multiple underscores
        $cleanName = preg_replace('/_+/', '_', $cleanName);
        
        // Hapus underscore di awal dan akhir
        $cleanName = trim($cleanName, '_');
        
        return $cleanName;
    }

    private function addDebugInfo(string $action, array $data): void
    {
        $this->debugInfo[] = [
            'time' => now()->format('H:i:s.u'),
            'action' => $action,
            'data' => $data
        ];

        if (count($this->debugInfo) > 15) {
            $this->debugInfo = array_slice($this->debugInfo, -15);
        }
    }

    #[Computed]
    public function hasPdf(): bool
    {
        return !empty($this->pdfUrl);
    }

    #[Computed]
    public function hasQr(): bool
    {
        return !empty($this->qrUrl);
    }

    #[Computed]
    public function canPreview(): bool
    {
        return $this->hasPdf && $this->hasQr;
    }

    #[Computed]
    public function isTicketSelected(): bool
    {
        return !empty($this->ticketId);
    }

    #[Computed]
    public function availableFilesCount(): int
    {
        return count($this->getFileOptions());
    }

    #[Computed]
    public function hasCurrentDocument(): bool
    {
        return $this->currentDocument !== null;
    }

    #[Computed]
    public function canSaveDocument(): bool
    {
        return !empty($this->selectedFile) && !empty($this->ticketId);
    }

    // Add method to refresh tickets manually if needed
    public function refreshTickets(): void
    {
        $this->addDebugInfo('Manual tickets refresh', [
            'before_count' => count($this->getTicketOptions())
        ]);
        
        // Clear any cached data
        $this->ticketId = null;
        $this->selectedFile = null;
        $this->currentDocument = null;
        
        // Reset form
        $this->form->fill([
            'ticketId' => null,
            'selectedFile' => null,
            'document_name' => '',
            'document_description' => '',
            'document_signer' => '',
            'document_recipient' => '',
            'document_action' => '',
            'document_number' => '',
            'notes' => ''
        ]);
        
        // This will trigger a re-render and refresh the select options
        $this->dispatch('$refresh');
        
        Notification::make()
            ->title('Refreshed')
            ->body('Daftar tiket telah diperbarui. Count: ' . count($this->getTicketOptions()))
            ->info()
            ->send();
    }
    public function testTicketData(): void
    {
        try {
            // Test 1: Basic ticket count
            $totalTickets = Ticket::count();
            
            // Test 2: Get first 5 tickets with all fields
            $sampleTickets = Ticket::select( 'uuid', 'ticket_code', 'created_at')
                ->orderBy('ticket_code', 'desc')
                ->limit(5)
                ->get();
            
            // Test 3: Count tickets with UUID
            $withUuid = Ticket::whereNotNull('uuid')->where('uuid', '!=', '')->count();
            
            // Test 4: Count tickets with ticket_code
            $withCode = Ticket::whereNotNull('ticket_code')->where('ticket_code', '!=', '')->count();
            
            // Test 5: Get valid tickets
            $validTickets = Ticket::select('uuid', 'ticket_code')
                ->whereNotNull('uuid')
                ->whereNotNull('ticket_code')
                ->where('uuid', '!=', '')
                ->where('ticket_code', '!=', '')
                ->get();
            
            $debugData = [
                'total_tickets' => $totalTickets,
                'sample_tickets' => $sampleTickets->toArray(),
                'tickets_with_uuid' => $withUuid,
                'tickets_with_code' => $withCode,
                'valid_tickets_count' => $validTickets->count(),
                'valid_tickets' => $validTickets->toArray(),
                'options_generated' => $this->getTicketOptions()
            ];
            
            Log::info('Ticket Data Debug:', $debugData);
            
            Notification::make()
                ->title('Debug Info')
                ->body('Check Laravel log for detailed ticket data. Total: ' . $totalTickets . ', Valid: ' . $validTickets->count())
                ->info()
                ->send();
                
        } catch (\Exception $e) {
            Log::error('Error in testTicketData:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->title('Error')
                ->body('Error testing ticket data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}