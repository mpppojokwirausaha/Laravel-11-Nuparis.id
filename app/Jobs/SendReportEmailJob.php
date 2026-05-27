<?php

namespace App\Jobs;

use App\Mail\ReportExportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected string $to;
    protected array $ccList;
    protected string $subject;
    protected string $body;
    protected string $pdfPath;
    protected string $fileName;

    public function __construct(
        string $to,
        array $ccList,
        string $subject,
        string $body,
        string $pdfContent,
        string $fileName
    ) {
        $this->to = $to;
        $this->ccList = $ccList;
        $this->subject = $subject;
        $this->body = $body;
        $this->fileName = $fileName;

        // Simpan PDF ke temporary file
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $this->pdfPath = $tempDir . '/' . uniqid() . '_' . $fileName;

        // Simpan PDF ke file
        file_put_contents($this->pdfPath, $pdfContent);

        Log::info('Job: PDF saved to temporary file', [
            'path' => $this->pdfPath,
            'size' => strlen($pdfContent)
        ]);
    }

    public function handle(): void
    {
        Log::info('Job: STARTING - Memulai pengiriman email', [
            'to' => $this->to,
            'subject' => $this->subject,
            'pdf_path' => $this->pdfPath,
            'attempt' => $this->attempts()
        ]);

        try {
            // Cek apakah file PDF masih ada
            if (!file_exists($this->pdfPath)) {
                throw new \Exception("PDF file not found: {$this->pdfPath}");
            }

            // Baca PDF dari file
            $pdfContent = file_get_contents($this->pdfPath);

            $mailable = new ReportExportMail(
                subject: $this->subject,
                body: $this->body,
                pdfContent: $pdfContent,
                fileName: $this->fileName,
            );

            $mail = Mail::to($this->to);
            if (!empty($this->ccList)) {
                $mail->cc($this->ccList);
            }

            $mail->send($mailable);

            Log::info('Job: SUCCESS - Email berhasil dikirim', [
                'to' => $this->to,
                'subject' => $this->subject
            ]);

            // Hapus file setelah sukses
            if (file_exists($this->pdfPath)) {
                unlink($this->pdfPath);
                Log::info('Job: PDF file deleted after success', ['path' => $this->pdfPath]);
            }
        } catch (\Exception $e) {
            Log::error('Job: FAILED - Gagal mengirim email', [
                'to' => $this->to,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job: FAILED AFTER RETRIES', [
            'to' => $this->to,
            'subject' => $this->subject,
            'error' => $exception->getMessage(),
            'pdf_path' => $this->pdfPath
        ]);

        // Hapus file jika masih ada setelah gagal
        if (isset($this->pdfPath) && file_exists($this->pdfPath)) {
            unlink($this->pdfPath);
            Log::info('Job: PDF file deleted after failure', ['path' => $this->pdfPath]);
        }
    }
}
