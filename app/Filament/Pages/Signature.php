<?php

namespace App\Filament\Pages;

use App\Models\DocumentToss;
use App\Models\Ticket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
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

    private string $gsPath = 'gs';

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

        // Cek Ghostscript
        $this->checkGhostscript();
    }

    /**
     * Cek ketersediaan Ghostscript dan dapatkan path-nya
     */
    private function checkGhostscript(): void
    {
        // Cek di path yang sudah diketahui (Linux)
        $possiblePaths = [
            '/usr/bin/gs',
            '/bin/gs',
            '/usr/local/bin/gs',
            '/usr/local/opt/ghostscript/bin/gs',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->gsPath = $path;
                Log::info('[Signature] Ghostscript found at', ['path' => $this->gsPath]);
                return;
            }
        }

        // Coba dengan 'which gs'
        $output = [];
        $code = 0;
        exec('which gs 2>&1', $output, $code);
        if ($code === 0 && !empty($output[0]) && file_exists(trim($output[0]))) {
            $this->gsPath = trim($output[0]);
            Log::info('[Signature] Ghostscript found in PATH', ['path' => $this->gsPath]);
            return;
        }

        // Fallback ke 'gs' (biar dicari di PATH)
        $this->gsPath = 'gs';
        Log::warning('[Signature] Ghostscript using default PATH', ['path' => $this->gsPath]);

        // Test apakah gs bisa dijalankan
        exec('gs --version 2>&1', $versionOutput, $versionCode);
        if ($versionCode === 0) {
            Log::info('[Signature] Ghostscript version', ['version' => implode('', $versionOutput)]);
        } else {
            Log::error('[Signature] Ghostscript NOT available on this server');
        }
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

    // ── Generate QR Code dengan endroid/qr-code ────────────────────────────────
    private function generateQrCode(string $content, string $path): void
    {
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($content)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        Storage::disk('public')->put($path, $qrCode->getString());
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

            $this->generateQrCode($qrContent, $qrPath);

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

    // ===========================================================================
    // ======================== CORE FUNCTIONS ===================================
    // ===========================================================================

    /**
     * Render PDF ke PNG menggunakan Ghostscript
     */
    private function renderPdfToPng(string $pdfPath, string $pngPath, int $page): void
    {
        // Hapus file lama jika ada
        if (file_exists($pngPath)) {
            @unlink($pngPath);
        }

        // Pastikan direktori tujuan ada
        $dir = dirname($pngPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Escape untuk shell
        $escapedPdf = escapeshellarg($pdfPath);
        $escapedPng = escapeshellarg($pngPath);

        // Build command - gunakan $this->gsPath yang sudah di-set
        $command = sprintf(
            '%s -dQUIET -dBATCH -dNOPAUSE -dNOPROMPT '
                . '-sDEVICE=png16m -r150 '
                . '-dFirstPage=%d -dLastPage=%d '
                . '-sOutputFile=%s %s 2>&1',
            $this->gsPath,
            $page,
            $page,
            $escapedPng,
            $escapedPdf
        );

        Log::info('[Signature] gs render PNG', ['cmd' => $command, 'gsPath' => $this->gsPath]);

        // Gunakan exec langsung
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        Log::info('[Signature] gs render result', [
            'returnCode' => $returnCode,
            'output' => implode("\n", $output),
            'fileExists' => file_exists($pngPath),
            'fileSize' => file_exists($pngPath) ? filesize($pngPath) : 0
        ]);

        // Jika return code 127 (command not found), coba dengan 'gs' saja
        if ($returnCode === 127 && $this->gsPath !== 'gs') {
            Log::info('[Signature] Retry with fallback gs command');
            $fallbackCommand = sprintf(
                'gs -dQUIET -dBATCH -dNOPAUSE -dNOPROMPT '
                    . '-sDEVICE=png16m -r150 '
                    . '-dFirstPage=%d -dLastPage=%d '
                    . '-sOutputFile=%s %s 2>&1',
                $page,
                $page,
                $escapedPng,
                $escapedPdf
            );

            exec($fallbackCommand, $output, $returnCode);

            Log::info('[Signature] gs render result (fallback)', [
                'returnCode' => $returnCode,
                'output' => implode("\n", $output),
            ]);
        }

        // Tunggu file selesai ditulis
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            clearstatcache(true, $pngPath);
            if (file_exists($pngPath) && filesize($pngPath) > 0) {
                break;
            }
            usleep(200000);
        }

        if (!file_exists($pngPath) || filesize($pngPath) === 0) {
            throw new \Exception("File PNG hasil render kosong: {$pngPath}. Return code: {$returnCode}");
        }

        // Validasi PNG
        $img = @imagecreatefrompng($pngPath);
        if (!$img) {
            throw new \Exception("File PNG tidak valid: {$pngPath}");
        }
        imagedestroy($img);

        Log::info('[Signature] PNG render success', [
            'size' => filesize($pngPath),
            'path' => $pngPath
        ]);
    }

    /**
     * Composite QR Code ke PNG menggunakan GD
     */
    private function compositeQrToPng(string $pageImgPath, string $qrPath, string $outputPath, float $ratioX, float $ratioY): void
    {
        // Load gambar utama
        $img = imagecreatefrompng($pageImgPath);
        if (!$img) {
            throw new \Exception('Gagal load PNG hasil render: ' . $pageImgPath);
        }

        // Load QR Code
        $qr = imagecreatefrompng($qrPath);
        if (!$qr) {
            imagedestroy($img);
            throw new \Exception('Gagal load QR Code: ' . $qrPath);
        }

        $imgW = imagesx($img);
        $imgH = imagesy($img);
        $qrW = imagesx($qr);
        $qrH = imagesy($qr);

        // Resize QR ke ukuran tetap (150x150)
        $targetSize = 150;
        $newQr = imagecreatetruecolor($targetSize, $targetSize);

        // Preserve transparency
        imagealphablending($newQr, false);
        imagesavealpha($newQr, true);
        $transparent = imagecolorallocatealpha($newQr, 0, 0, 0, 127);
        imagefill($newQr, 0, 0, $transparent);

        // Resample QR
        imagecopyresampled($newQr, $qr, 0, 0, 0, 0, $targetSize, $targetSize, $qrW, $qrH);

        // Hitung posisi
        $xPx = (int)($ratioX * $imgW);
        $yPx = (int)($ratioY * $imgH);
        $xPx = max(0, min($xPx, $imgW - $targetSize));
        $yPx = max(0, min($yPx, $imgH - $targetSize));

        Log::info('[Signature] Composite QR dengan GD', [
            'imgW' => $imgW,
            'imgH' => $imgH,
            'xPx' => $xPx,
            'yPx' => $yPx,
        ]);

        // Tempel QR
        imagecopy($img, $newQr, $xPx, $yPx, 0, 0, $targetSize, $targetSize);
        imagepng($img, $outputPath);

        // Bersihkan memory
        imagedestroy($img);
        imagedestroy($qr);
        imagedestroy($newQr);

        // Validasi hasil
        if (!file_exists($outputPath) || filesize($outputPath) === 0) {
            throw new \Exception("File PNG hasil composite tidak valid: {$outputPath}");
        }

        Log::info('[Signature] Composite success', ['size' => filesize($outputPath)]);
    }

    /**
     * Convert PNG ke PDF dengan multiple fallback methods
     */
    private function convertPngToPdfWithFallback(string $pngPath, string $pdfPath): bool
    {
        // Method 1: Ghostscript dengan berbagai parameter
        if ($this->convertWithGhostscriptVariants($pngPath, $pdfPath)) {
            Log::info('[Signature] Konversi sukses dengan Ghostscript');
            return true;
        }

        // Method 2: ImageMagick CLI (convert command)
        if ($this->convertWithImageMagickCli($pngPath, $pdfPath)) {
            Log::info('[Signature] Konversi sukses dengan ImageMagick CLI');
            return true;
        }

        // Method 3: FPDF (pure PHP)
        if ($this->convertWithFpdf($pngPath, $pdfPath)) {
            Log::info('[Signature] Konversi sukses dengan FPDF');
            return true;
        }

        Log::error('[Signature] Semua metode konversi PNG ke PDF gagal');
        return false;
    }

    /**
     * Method 1: Ghostscript dengan multiple variants
     */
    private function convertWithGhostscriptVariants(string $pngPath, string $pdfPath): bool
    {
        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        $variants = [
            // Variant 1: Standard
            sprintf(
                '%s -dQUIET -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s 2>&1',
                $this->gsPath,
                escapeshellarg($pdfPath),
                escapeshellarg($pngPath)
            ),
            // Variant 2: Dengan flatten transparency
            sprintf(
                '%s -dQUIET -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dHaveTransparency=false -sOutputFile=%s %s 2>&1',
                $this->gsPath,
                escapeshellarg($pdfPath),
                escapeshellarg($pngPath)
            ),
            // Variant 3: Dengan ColorConversionStrategy
            sprintf(
                '%s -dQUIET -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dColorConversionStrategy=/sRGB -dProcessColorModel=/DeviceRGB -sOutputFile=%s %s 2>&1',
                $this->gsPath,
                escapeshellarg($pdfPath),
                escapeshellarg($pngPath)
            ),
        ];

        foreach ($variants as $index => $command) {
            Log::info("[Signature] Ghostscript variant " . ($index + 1), ['cmd' => $command]);

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($pdfPath) && filesize($pdfPath) > 0) {
                Log::info("[Signature] Ghostscript variant " . ($index + 1) . " success");
                return true;
            }
        }

        return false;
    }

    /**
     * Method 2: Convert dengan ImageMagick CLI
     */
    private function convertWithImageMagickCli(string $pngPath, string $pdfPath): bool
    {
        // Cek apakah convert command tersedia
        $output = [];
        $code = 0;
        exec('convert --version 2>&1', $output, $code);

        if ($code !== 0) {
            Log::info('[Signature] ImageMagick convert command not available');
            return false;
        }

        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        $variants = [
            "convert %s %s 2>&1",
            "convert -density 150 -quality 100 %s %s 2>&1",
            "convert -flatten %s %s 2>&1",
            "convert -background white -alpha remove -alpha off %s %s 2>&1",
        ];

        foreach ($variants as $index => $variant) {
            $command = sprintf($variant, escapeshellarg($pngPath), escapeshellarg($pdfPath));
            Log::info("[Signature] ImageMagick variant " . ($index + 1), ['cmd' => $command]);

            $output = [];
            $code = 0;
            exec($command, $output, $code);

            if ($code === 0 && file_exists($pdfPath) && filesize($pdfPath) > 0) {
                Log::info("[Signature] ImageMagick variant " . ($index + 1) . " success");
                return true;
            }
        }

        return false;
    }

    /**
     * Method 3: Convert dengan FPDF (pure PHP)
     */
    private function convertWithFpdf(string $pngPath, string $pdfPath): bool
    {
        // Cek apakah FPDF tersedia
        if (!class_exists('FPDF')) {
            $fpdfPath = base_path('vendor/setasign/fpdf/fpdf.php');
            if (file_exists($fpdfPath)) {
                require_once $fpdfPath;
            } else {
                Log::warning('[Signature] FPDF not installed. Install with: composer require setasign/fpdf');
                return false;
            }
        }

        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        try {
            $pdf = new \FPDF();

            // Dapatkan dimensi gambar
            list($width, $height) = getimagesize($pngPath);

            // Konversi pixel ke mm (asumsi 150 DPI)
            $widthMm = ($width / 150) * 25.4;
            $heightMm = ($height / 150) * 25.4;

            // Tentukan orientasi
            $orientation = $widthMm > $heightMm ? 'L' : 'P';

            // Add page dengan ukuran custom
            $pdf->AddPage($orientation, [$widthMm, $heightMm]);

            // Tambahkan gambar
            $pdf->Image($pngPath, 0, 0, $widthMm, $heightMm);

            // Simpan ke file
            $pdf->Output('F', $pdfPath);

            if (file_exists($pdfPath) && filesize($pdfPath) > 0) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error('[Signature] FPDF conversion exception', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Extract halaman PDF
     */
    private function extractPdfPages(string $sourcePdf, string $outputPdf, int $startPage, int $endPage): void
    {
        $command = sprintf(
            '%s -dQUIET -dSAFER -dBATCH -dNOPAUSE '
                . '-sDEVICE=pdfwrite -dFirstPage=%d -dLastPage=%d '
                . '-sOutputFile=%s %s 2>&1',
            $this->gsPath,
            $startPage,
            $endPage,
            escapeshellarg($outputPdf),
            escapeshellarg($sourcePdf)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::warning('[Signature] Extract pages failed', ['cmd' => $command, 'output' => $output]);
        }
    }

    /**
     * Merge multiple PDF parts menjadi satu PDF
     */
    private function mergePdfParts(array $parts, string $outputPath): void
    {
        if (empty($parts)) {
            throw new \Exception('Tidak ada bagian PDF untuk di-merge');
        }

        $partsList = implode(' ', array_map('escapeshellarg', $parts));

        $command = sprintf(
            '%s -dQUIET -dSAFER -dBATCH -dNOPAUSE '
                . '-sDEVICE=pdfwrite -sOutputFile=%s %s 2>&1',
            $this->gsPath,
            escapeshellarg($outputPath),
            $partsList
        );

        Log::info('[Signature] gs merge', ['cmd' => $command]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Ghostscript merge gagal: ' . implode("\n", $output));
        }
    }

    /**
     * Get total halaman PDF
     */
    private function getPdfPageCount(string $pdfAbsPath): int
    {
        try {
            // Cara 1: gs
            $command = sprintf(
                '%s -dQUIET -dSAFER -dBATCH -dNOPAUSE -dNODISPLAY -c "(%s) (r) file runpdfbegin pdfpagecount = quit" 2>&1',
                $this->gsPath,
                addslashes($pdfAbsPath)
            );

            $output = [];
            $code = 0;
            exec($command, $output, $code);
            $out = trim(implode("\n", $output));

            if (is_numeric($out) && (int) $out > 0) {
                return (int) $out;
            }

            // Cara 2: pdfinfo (poppler-utils)
            $output = [];
            $code = 0;
            exec('pdfinfo ' . escapeshellarg($pdfAbsPath) . ' 2>&1', $output, $code);
            $outputStr = implode("\n", $output);

            if (preg_match('/Pages:\s*(\d+)/', $outputStr, $matches)) {
                return (int) $matches[1];
            }
        } catch (\Exception $e) {
            Log::warning('[Signature] getPdfPageCount failed', ['error' => $e->getMessage()]);
        }

        return 1;
    }

    // ===========================================================================
    // ======================== SAVE POSITION ====================================
    // ===========================================================================

    /**
     * Save QR Position — Main Function
     */
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
            $disk = Storage::disk('public');
            $ticket = Ticket::where('uuid', $this->ticketId)->first();
            if (! $ticket) throw new \Exception('Ticket tidak ditemukan');

            $slug = $this->buildSlug($ticket);

            // Path relatif
            $tossRelDir   = 'Tosses';
            $qrRelPath    = "qrcodes/{$slug}.png";
            $finalRelPath = "{$tossRelDir}/{$slug}_final.pdf";

            // Path absolut
            $pdfAbs     = $disk->path($this->selectedFile);
            $qrAbs      = $disk->path($qrRelPath);
            $tossAbsDir = $disk->path($tossRelDir);
            $finalAbs   = $disk->path($finalRelPath);

            // File temp
            $pageImgAbs   = "{$tossAbsDir}/{$slug}_page{$page}.png";
            $mergedPngAbs = "{$tossAbsDir}/{$slug}_merged{$page}.png";
            $mergedPdfAbs = "{$tossAbsDir}/{$slug}_merged{$page}.pdf";
            $beforePdfAbs = "{$tossAbsDir}/{$slug}_before.pdf";
            $afterPdfAbs  = "{$tossAbsDir}/{$slug}_after.pdf";

            Log::info('[Signature] savePosition START (GD + endroid version)', [
                'slug'        => $slug,
                'page'        => $page,
                'ratioX'      => $ratioX,
                'ratioY'      => $ratioY,
                'tossAbsDir'  => $tossAbsDir,
                'pdfExists'   => file_exists($pdfAbs),
                'qrExists'    => file_exists($qrAbs),
                'gsPath'      => $this->gsPath,
            ]);

            // ── Pastikan direktori Tosses ada ──────────────────────────────────
            if (! is_dir($tossAbsDir)) {
                mkdir($tossAbsDir, 0755, true);
            }

            // ── Validasi file input ────────────────────────────────────────────
            if (! file_exists($pdfAbs)) {
                throw new \Exception("File PDF tidak ditemukan: {$pdfAbs}");
            }
            if (! file_exists($qrAbs)) {
                $disk->makeDirectory('qrcodes');
                $qrContent = url('toss/' . $slug);
                $this->generateQrCode($qrContent, $qrRelPath);
                Log::info('[Signature] QR regenerated with endroid', ['path' => $qrAbs]);
            }

            // ── Step 1: Render halaman PDF → PNG ────────────────────────────────
            $this->renderPdfToPng($pdfAbs, $pageImgAbs, $page);

            // ── Step 2: Composite QR ke PNG ─────────────────────────────────────
            $this->compositeQrToPng($pageImgAbs, $qrAbs, $mergedPngAbs, $ratioX, $ratioY);

            // ── Step 3: Convert PNG → PDF ───────────────────────────────────────
            $converted = $this->convertPngToPdfWithFallback($mergedPngAbs, $mergedPdfAbs);

            if (!$converted) {
                throw new \Exception('Semua metode konversi PNG ke PDF gagal');
            }

            // ── Step 4: Extract halaman sebelum & sesudah ───────────────────────
            $totalPages = $this->getPdfPageCount($pdfAbs);
            $parts = [];

            Log::info('[Signature] PDF total pages', ['total' => $totalPages, 'page' => $page]);

            // Halaman sebelum
            if ($page > 1) {
                $this->extractPdfPages($pdfAbs, $beforePdfAbs, 1, $page - 1);
                if (file_exists($beforePdfAbs) && filesize($beforePdfAbs) > 0) {
                    $parts[] = $beforePdfAbs;
                }
            }

            // Halaman yang sudah ditempeli QR
            $parts[] = $mergedPdfAbs;

            // Halaman sesudah
            if ($page < $totalPages) {
                $this->extractPdfPages($pdfAbs, $afterPdfAbs, $page + 1, $totalPages);
                if (file_exists($afterPdfAbs) && filesize($afterPdfAbs) > 0) {
                    $parts[] = $afterPdfAbs;
                }
            }

            // ── Step 5: Merge semua bagian → final PDF ─────────────────────────
            $this->mergePdfParts($parts, $finalAbs);

            if (! file_exists($finalAbs) || filesize($finalAbs) === 0) {
                throw new \Exception("File final tidak ada atau kosong: {$finalAbs}");
            }

            // ── Step 6: Cleanup temp files ─────────────────────────────────────
            foreach ([$pageImgAbs, $mergedPngAbs, $mergedPdfAbs, $beforePdfAbs, $afterPdfAbs] as $tmp) {
                if ($tmp && file_exists($tmp)) {
                    @unlink($tmp);
                }
            }

            // ── Step 7: Simpan ke database ─────────────────────────────────────
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

            Log::info('[Signature] DONE (GD + endroid version)', [
                'finalRelPath' => $finalRelPath,
                'finalSize'    => filesize($finalAbs),
            ]);

            Notification::make()
                ->title('Berhasil Disimpan')
                ->body("File: {$finalRelPath} (" . round(filesize($finalAbs) / 1024) . " KB)")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('[Signature] savePosition FAILED (GD + endroid version)', [
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
        if (preg_match('/progress-?(\d+)/', $fileName, $matches)) {
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
