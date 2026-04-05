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

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static string  $view            = 'filament.pages.signature';
    protected static ?string $title           = 'Signature';
    protected static ?string $navigationGroup = 'TOSS';

    public ?string $ticketId        = null;
    public ?string $selectedFile    = null;
    public ?string $pdfUrl          = null;
    public ?string $qrUrl           = null;
    public ?array  $data            = [];
    public array   $debugInfo       = [];
    public ?DocumentToss $currentDocument = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_Signature');
    }

    public function mount(): void
    {
        $this->ticketId        = null;
        $this->selectedFile    = null;
        $this->currentDocument = null;
        $this->form->fill($this->emptyFormData());
        $this->addDebugInfo('Page mounted', [
            'tickets_count' => count($this->getTicketOptions()),
        ]);
    }

    // ── Form ───────────────────────────────────────────────────────────────────
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
                                $this->ticketId        = $state;
                                $this->selectedFile    = null;
                                $this->currentDocument = null;
                                $this->clearPreview();
                                $this->form->fill(array_merge($this->emptyFormData(), ['ticketId' => $state]));
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
                            ->columnSpan(2)
                            ->required()
                            ->maxLength(255),

                        Textarea::make('document_description')
                            ->label('Deskripsi Dokumen')
                            ->columnSpan(2)
                            ->required()
                            ->rows(3),

                        TextInput::make('document_bySign')
                            ->label('Penandatangan')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('document_toReceive')
                            ->label('Penerima Dokumen')
                            ->required()
                            ->maxLength(255),

                        Select::make('document_action')
                            ->label('Tindakan Dokumen')
                            ->options(['Tanda Tangan' => 'Tanda Tangan'])
                            ->required(),

                        TextInput::make('document_no')
                            ->label('Nomor Dokumen')
                            ->required()
                            ->maxLength(100),

                        DateTimePicker::make('document_start')
                            ->label('Tanggal Mulai Aktif')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->default(now()->format('d/m/Y')),

                        DateTimePicker::make('document_end')
                            ->label('Tanggal Berakhir')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->default(null),

                        RichEditor::make('document_notes')
                            ->label('Catatan')
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
        if (! $this->ticketId || ! $this->selectedFile) return;

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
            $this->addDebugInfo('Error loading document', ['error' => $e->getMessage()]);
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
            return $options;
        } catch (\Exception $e) {
            Log::error('Error loading tickets', ['error' => $e->getMessage()]);
            Notification::make()->title('Error')->body('Gagal memuat tiket: ' . $e->getMessage())->danger()->send();
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
            return $files;
        } catch (\Exception $e) {
            Notification::make()->title('Error')->body('Gagal memuat file: ' . $e->getMessage())->danger()->send();
            return [];
        }
    }

    // ── Generate Preview ───────────────────────────────────────────────────────
    public function generatePreview(): void
    {
        try {
            if (! $this->ticketId || ! $this->selectedFile) {
                Notification::make()->title('Warning')->body('Pilih ticket dan file terlebih dahulu.')->warning()->send();
                return;
            }

            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) throw new \Exception('Ticket tidak ditemukan');

            if (! Storage::disk('public')->exists($this->selectedFile)) {
                throw new \Exception('File PDF tidak ditemukan: ' . $this->selectedFile);
            }

            $slug    = $this->buildSlug($ticket);
            $qrPath  = "qrcodes/{$slug}.png";

            Storage::disk('public')->makeDirectory('qrcodes');
            $qrContent = url('toss/' . $slug);
            Storage::disk('public')->put($qrPath, QrCode::format('png')->size(300)->generate($qrContent));

            $this->pdfUrl = Storage::disk('public')->url($this->selectedFile);
            $this->qrUrl  = Storage::disk('public')->url($qrPath);

            $this->dispatch('preview-updated', ['pdfUrl' => $this->pdfUrl, 'qrUrl' => $this->qrUrl]);
            Notification::make()->title('Success')->body('Preview berhasil di-generate!')->success()->send();
        } catch (\Exception $e) {
            Log::error('[Signature] generatePreview error', ['error' => $e->getMessage()]);
            Notification::make()->title('Error')->body('Gagal generate preview: ' . $e->getMessage())->danger()->send();
        }
    }

    // ── Clear Preview ──────────────────────────────────────────────────────────
    public function clearPreview(): void
    {
        $this->pdfUrl = null;
        $this->qrUrl  = null;
        $this->dispatch('preview-cleared');
    }

    // ── Save QR Position — menggunakan gs (Ghostscript) ────────────────────────
    #[On('save-qr-position')]
    public function savePosition(
        float $x,
        float $y,
        float $scale,
        int   $page        = 1,
        float $canvasWidth  = 892,
        float $canvasHeight = 1262,
        float $renderScale  = 1.5,
        float $ratioX       = 0.5,
        float $ratioY       = 0.5
    ): void {
        try {
            // ── Resolve path ───────────────────────────────────────────────────
            $disk      = Storage::disk('public');
            $diskRoot  = $disk->path('');  // absolut root storage/app/public

            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) throw new \Exception('Ticket tidak ditemukan');

            $slug = $this->buildSlug($ticket);

            // Path relatif (untuk Storage & DB)
            $tossRelDir   = 'Tosses';
            $qrRelPath    = "qrcodes/{$slug}.png";
            $finalRelPath = "{$tossRelDir}/{$slug}_final.pdf";

            // Path absolut (untuk Imagick, gs, file_exists)
            $pdfAbs     = $disk->path($this->selectedFile);
            $qrAbs      = $disk->path($qrRelPath);
            $tossAbsDir = $disk->path($tossRelDir);
            $finalAbs   = $disk->path($finalRelPath);

            // File temp absolut
            $pageImgAbs   = "{$tossAbsDir}/{$slug}_page{$page}.png";
            $mergedPngAbs = "{$tossAbsDir}/{$slug}_merged{$page}.png";
            $mergedPdfAbs = "{$tossAbsDir}/{$slug}_merged{$page}.pdf";
            $beforePdfAbs = "{$tossAbsDir}/{$slug}_before.pdf";
            $afterPdfAbs  = "{$tossAbsDir}/{$slug}_after.pdf";

            Log::info('[Signature] savePosition START', [
                'slug'        => $slug,
                'page'        => $page,
                'ratioX'      => $ratioX,
                'ratioY'      => $ratioY,
                'diskRoot'    => $diskRoot,
                'tossAbsDir'  => $tossAbsDir,
                'finalAbs'    => $finalAbs,
                'pdfExists'   => file_exists($pdfAbs),
                'qrExists'    => file_exists($qrAbs),
            ]);

            // ── Pastikan direktori Tosses ada ──────────────────────────────────
            if (! is_dir($tossAbsDir)) {
                mkdir($tossAbsDir, 0755, true);
            }
            // Pastikan Storage juga mengenali direktori ini
            if (! $disk->exists($tossRelDir)) {
                $disk->makeDirectory($tossRelDir);
            }

            // ── Validasi file input ────────────────────────────────────────────
            if (! file_exists($pdfAbs)) {
                throw new \Exception("File PDF tidak ditemukan: {$pdfAbs}");
            }
            if (! file_exists($qrAbs)) {
                // Regenerate QR jika hilang
                $disk->makeDirectory('qrcodes');
                $disk->put($qrRelPath, QrCode::format('png')->size(300)->generate(url('toss/' . $slug)));
                Log::info('[Signature] QR regenerated', ['path' => $qrAbs]);
            }

            // ── Step 1: Render halaman PDF → PNG menggunakan gs ───────────────
            // gs render halaman ke PNG, 150dpi, tanpa butuh GUI
            $gsRenderCmd = sprintf(
                'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT '
                    . '-sDEVICE=png16m -r150 '
                    . '-dFirstPage=%d -dLastPage=%d '
                    . '-sOutputFile=%s %s',
                $page,
                $page,
                escapeshellarg($pageImgAbs),
                escapeshellarg($pdfAbs)
            );

            Log::info('[Signature] gs render PNG', ['cmd' => $gsRenderCmd]);
            $procRender = Process::fromShellCommandline($gsRenderCmd);
            $procRender->run();

            Log::info('[Signature] gs render result', [
                'ok'      => $procRender->isSuccessful(),
                'stdout'  => $procRender->getOutput(),
                'stderr'  => $procRender->getErrorOutput(),
                'imgExists' => file_exists($pageImgAbs),
                'imgSize'   => file_exists($pageImgAbs) ? filesize($pageImgAbs) : 0,
            ]);

            if (! $procRender->isSuccessful() || ! file_exists($pageImgAbs)) {
                throw new \Exception('gs gagal render halaman: ' . $procRender->getErrorOutput());
            }

            // ── Step 2: Composite QR ke PNG menggunakan Imagick ───────────────
            $img = new Imagick($pageImgAbs);
            $qr  = new Imagick($qrAbs);

            $imgW     = $img->getImageWidth();
            $imgH     = $img->getImageHeight();
            $qrSizePx = 150; // ukuran QR fixed di file output

            $qr->resizeImage($qrSizePx, $qrSizePx, Imagick::FILTER_LANCZOS, 1);

            // Posisi dari rasio (0..1) terhadap dimensi PNG
            $xPx = (int) ($ratioX * $imgW);
            $yPx = (int) ($ratioY * $imgH);
            $xPx = max(0, min($xPx, $imgW - $qrSizePx));
            $yPx = max(0, min($yPx, $imgH - $qrSizePx));

            Log::info('[Signature] Composite QR', [
                'imgW' => $imgW,
                'imgH' => $imgH,
                'xPx'  => $xPx,
                'yPx'  => $yPx,
            ]);

            $img->compositeImage($qr, Imagick::COMPOSITE_OVER, $xPx, $yPx);
            $img->writeImage($mergedPngAbs);

            // Convert PNG → single-page PDF
            $img->setImageFormat('pdf');
            $img->writeImage($mergedPdfAbs);

            $img->clear();
            $img->destroy();
            $qr->clear();
            $qr->destroy();

            Log::info('[Signature] Merged PDF written', [
                'path'   => $mergedPdfAbs,
                'exists' => file_exists($mergedPdfAbs),
                'size'   => file_exists($mergedPdfAbs) ? filesize($mergedPdfAbs) : 0,
            ]);

            // ── Step 3: Extract halaman sebelum & sesudah menggunakan gs ──────
            $totalPages = $this->getPdfPageCount($pdfAbs);
            $parts      = [];

            Log::info('[Signature] PDF total pages', ['total' => $totalPages, 'page' => $page]);

            // Halaman sebelum halaman target
            if ($page > 1) {
                $gsBeforeCmd = sprintf(
                    'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE '
                        . '-sDEVICE=pdfwrite -dFirstPage=1 -dLastPage=%d '
                        . '-sOutputFile=%s %s',
                    $page - 1,
                    escapeshellarg($beforePdfAbs),
                    escapeshellarg($pdfAbs)
                );
                $procBefore = Process::fromShellCommandline($gsBeforeCmd);
                $procBefore->run();
                Log::info('[Signature] gs extract before', [
                    'ok'     => $procBefore->isSuccessful(),
                    'stderr' => $procBefore->getErrorOutput(),
                    'exists' => file_exists($beforePdfAbs),
                    'size'   => file_exists($beforePdfAbs) ? filesize($beforePdfAbs) : 0,
                ]);
                if (file_exists($beforePdfAbs) && filesize($beforePdfAbs) > 0) {
                    $parts[] = $beforePdfAbs;
                }
            }

            // Halaman yang sudah ditempeli QR
            $parts[] = $mergedPdfAbs;

            // Halaman sesudah halaman target
            if ($page < $totalPages) {
                $gsAfterCmd = sprintf(
                    'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE '
                        . '-sDEVICE=pdfwrite -dFirstPage=%d '
                        . '-sOutputFile=%s %s',
                    $page + 1,
                    escapeshellarg($afterPdfAbs),
                    escapeshellarg($pdfAbs)
                );
                $procAfter = Process::fromShellCommandline($gsAfterCmd);
                $procAfter->run();
                Log::info('[Signature] gs extract after', [
                    'ok'     => $procAfter->isSuccessful(),
                    'stderr' => $procAfter->getErrorOutput(),
                    'exists' => file_exists($afterPdfAbs),
                    'size'   => file_exists($afterPdfAbs) ? filesize($afterPdfAbs) : 0,
                ]);
                if (file_exists($afterPdfAbs) && filesize($afterPdfAbs) > 0) {
                    $parts[] = $afterPdfAbs;
                }
            }

            // ── Step 4: Merge semua bagian → final PDF ─────────────────────────
            $partsList = implode(' ', array_map('escapeshellarg', $parts));
            $gsMergeCmd = sprintf(
                'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE '
                    . '-sDEVICE=pdfwrite -sOutputFile=%s %s',
                escapeshellarg($finalAbs),
                $partsList
            );

            Log::info('[Signature] gs merge', [
                'cmd'   => $gsMergeCmd,
                'parts' => $parts,
            ]);

            $procMerge = Process::fromShellCommandline($gsMergeCmd);
            $procMerge->run();

            Log::info('[Signature] gs merge result', [
                'ok'          => $procMerge->isSuccessful(),
                'stderr'      => $procMerge->getErrorOutput(),
                'finalExists' => file_exists($finalAbs),
                'finalSize'   => file_exists($finalAbs) ? filesize($finalAbs) : 0,
                // Cek juga via Storage::disk
                'storageExists' => $disk->exists($finalRelPath),
                'storagePath'   => $disk->path($finalRelPath),
                'storageUrl'    => $disk->url($finalRelPath),
            ]);

            if (! $procMerge->isSuccessful()) {
                throw new \Exception('gs merge gagal: ' . $procMerge->getErrorOutput());
            }
            if (! file_exists($finalAbs) || filesize($finalAbs) === 0) {
                throw new \Exception("File final tidak ada atau kosong: {$finalAbs}");
            }

            // ── Step 5: Cleanup temp files ─────────────────────────────────────
            foreach ([$pageImgAbs, $mergedPngAbs, $mergedPdfAbs, $beforePdfAbs, $afterPdfAbs] as $tmp) {
                if ($tmp && file_exists($tmp)) {
                    unlink($tmp);
                }
            }

            // ── Step 6: Simpan ke database ─────────────────────────────────────
            DocumentToss::updateOrCreate(
                [
                    'ticket_code'   => $ticket->ticket_code,
                    'document_path' => $this->selectedFile,
                ],
                [
                    'qr_position_x'        => $ratioX,
                    'qr_position_y'        => $ratioY,
                    'qr_scale'             => $scale,
                    'scale'                => $scale,
                    'qr_page'              => $page,
                    'qr_path'              => $qrRelPath,
                    'document_name'        => $this->data['document_name']        ?? '',
                    'document_description' => $this->data['document_description'] ?? '',
                    'document_bySign'      => $this->data['document_bySign']      ?? '',
                    'document_toReceive'   => $this->data['document_toReceive']   ?? '',
                    'document_action'      => $this->data['document_action']      ?? '',
                    'document_no'          => $this->data['document_no']          ?? '',
                    'document_notes'       => $this->data['document_notes']       ?? '',
                    'document_final_path'  => $finalRelPath,
                    'document_slug'        => $slug,
                    'document_start'       => $this->data['document_start']       ?? null,
                    'document_end'         => $this->data['document_end']         ?? null,
                ]
            );

            Log::info('[Signature] DONE', [
                'finalRelPath' => $finalRelPath,
                'finalAbs'     => $finalAbs,
                'finalSize'    => filesize($finalAbs),
                'storageUrl'   => $disk->url($finalRelPath),
            ]);

            Notification::make()
                ->title('Berhasil Disimpan')
                ->body("File: {$finalRelPath} (" . round(filesize($finalAbs) / 1024) . " KB)")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('[Signature] savePosition FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Notification::make()
                ->title('Gagal Menyimpan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // ── Build slug ─────────────────────────────────────────────────────────────
    private function buildSlug(Ticket $ticket): string
    {
        $fileName      = pathinfo($this->selectedFile, PATHINFO_FILENAME);
        $progressIndex = $this->extractProgressIndex($fileName);
        $cleanFileName = $this->sanitizeFileName($fileName);
        return "{$ticket->ticket_code}_progress{$progressIndex}_{$cleanFileName}";
    }

    // ── Get total halaman PDF ──────────────────────────────────────────────────
    private function getPdfPageCount(string $pdfAbsPath): int
    {
        try {
            // Cara 1: gs
            $proc = Process::fromShellCommandline(
                'gs -dQUIET -dSAFER -dBATCH -dNOPAUSE -dNODISPLAY '
                    . '-c "(' . addslashes($pdfAbsPath) . ') (r) file runpdfbegin pdfpagecount = quit"'
            );
            $proc->run();
            $out = trim($proc->getOutput());
            if (is_numeric($out) && (int) $out > 0) {
                return (int) $out;
            }

            // Cara 2: pdfinfo (bagian dari poppler-utils)
            $proc2 = Process::fromShellCommandline(
                'pdfinfo ' . escapeshellarg($pdfAbsPath) . ' | grep "^Pages:" | awk \'{print $2}\''
            );
            $proc2->run();
            $out2 = trim($proc2->getOutput());
            if (is_numeric($out2) && (int) $out2 > 0) {
                return (int) $out2;
            }
        } catch (\Exception $e) {
            Log::warning('[Signature] getPdfPageCount failed', ['error' => $e->getMessage()]);
        }
        return 99; // fallback: asumsikan banyak halaman supaya after-range tetap diambil
    }

    // ── Debug ──────────────────────────────────────────────────────────────────
    #[On('refresh-debug')]
    public function refreshDebug(): void
    {
        $this->addDebugInfo('Debug refreshed', ['timestamp' => now()->toDateTimeString()]);
    }

    public function refreshTickets(): void
    {
        $this->ticketId        = null;
        $this->selectedFile    = null;
        $this->currentDocument = null;
        $this->form->fill($this->emptyFormData());
        $this->dispatch('$refresh');
        Notification::make()->title('Refreshed')->body('Daftar tiket diperbarui.')->info()->send();
    }

    // ── Private helpers ────────────────────────────────────────────────────────
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
        $this->debugInfo[] = ['time' => now()->format('H:i:s.u'), 'action' => $action, 'data' => $data];
        if (count($this->debugInfo) > 15) {
            $this->debugInfo = array_slice($this->debugInfo, -15);
        }
    }

    // ── Computed ───────────────────────────────────────────────────────────────
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
