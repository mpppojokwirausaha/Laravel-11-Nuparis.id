<?php

namespace App\Models;

use App\Models\ConsultantSpecialization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Ticket extends Model
{
    public $incrementing = false;
    protected $table = 'tickets';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
        'ticket_progress' => 'array'
    ];

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'ticket_code',
        'ticket_title',
        'ticket_name_client',
        'ticket_whatsapp',
        'ticket_email',
        'ticket_content',
        'ticket_status_uuid',
        'ticket_document_support',
        'consultant_specialization_uuid',
        'ticket_progress'
    ];

    protected static function booted()
    {
        // Auto generate UUID saat creating
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Setelah ticket disimpan, rename file
        static::saved(function ($ticket) {
            // \Log::info('Ticket saved, checking files...', [
            //     'ticket_code' => $ticket->ticket_code,
            //     'has_progress' => !empty($ticket->progress)
            // ]);

            // Panggil method untuk rename file
            $ticket->renameUploadedFiles();
        });
    }

    public function renameUploadedFiles()
    {
        // Pastikan ticket_code ada
        // if (!$this->ticket_code) {
        //     \Log::warning('No ticket_code found, skipping rename');
        //     return;
        // }

        $directory = 'tickets/' . $this->ticket_code;

        // \Log::info('=== START UPDATE URLs ===');
        // \Log::info('Ticket code: ' . $this->ticket_code);

        // // Cek apakah folder ada
        // if (!Storage::disk('public')->exists($directory)) {
        //     \Log::info('Directory does not exist: ' . $directory);
        //     return;
        // }

        // Ambil semua file di folder yang sudah di-rename (sudah ada prefix ticket_code)
        $files = Storage::disk('public')->files($directory);

        // Filter hanya file yang sudah memiliki prefix ticket_code
        $renamedFiles = [];
        foreach ($files as $file) {
            $filename = basename($file);
            if (strpos($filename, $this->ticket_code . '_') === 0) {
                $renamedFiles[] = [
                    'filename' => $filename,
                    'url' => Storage::disk('public')->url($file),
                    'extension' => pathinfo($filename, PATHINFO_EXTENSION)
                ];
            }
        }

        // if (empty($renamedFiles)) {
        //     \Log::info('No renamed files found');
        //     return;
        // }

        // \Log::info('Found renamed files:', array_column($renamedFiles, 'filename'));

        // Decode progress JSON
        // if (!$this->progress) {
        //     \Log::info('No progress data');
        //     return;
        // }

        $progressData = json_decode($this->progress, true);
        if (!is_array($progressData)) {
            \Log::error('Failed to decode progress JSON');
            return;
        }

        $updated = false;

        // Update setiap entry di progress
        foreach ($progressData as $index => &$entry) {
            if (!isset($entry['progress'])) {
                continue;
            }

            $content = $entry['progress'];
            $originalContent = $content;

            // Untuk setiap file yang sudah di-rename
            foreach ($renamedFiles as $file) {
                $newUrl = $file['url'];
                $newFilename = $file['filename'];
                $extension = $file['extension'];

                // Cari semua URL gambar dengan ekstensi yang sama di folder ini
                // Pattern untuk mencari URL dengan format apapun (random huruf besar/kecil)
                $pattern = '/https?:\/\/[^"\'\\s]+\/storage\/tickets\/' . preg_quote($this->ticket_code, '/') . '\/([A-Za-z0-9]+)\.' . preg_quote($extension, '/') . '/';

                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[0] as $oldUrl) {
                        $oldFilename = basename(parse_url($oldUrl, PHP_URL_PATH));

                        // Jika URL ini belum memiliki prefix ticket_code, update
                        // if (strpos($oldFilename, $this->ticket_code . '_') !== 0) {
                        //     \Log::info("Replacing URL in entry {$index}", [
                        //         'old' => $oldFilename,
                        //         'new' => $newFilename
                        //     ]);
                        //     $content = str_replace($oldUrl, $newUrl, $content);
                        // }
                    }
                }

                // Update juga di dalam data-trix-attachment JSON
                $content = preg_replace_callback(
                    '/data-trix-attachment="({[^"]+})"/',
                    function ($matches) use ($newUrl, $newFilename) {
                        $attachmentJson = html_entity_decode($matches[1]);
                        $attachment = json_decode($attachmentJson, true);

                        if ($attachment && isset($attachment['href'])) {
                            $oldHref = $attachment['href'];
                            $oldFilename = basename(parse_url($oldHref, PHP_URL_PATH));

                            // Jika filename belum memiliki prefix ticket_code
                            if (strpos($oldFilename, $this->ticket_code . '_') !== 0) {
                                $attachment['href'] = $newUrl;
                                $attachment['url'] = $newUrl;
                                return 'data-trix-attachment="' . htmlspecialchars(json_encode($attachment)) . '"';
                            }
                        }
                        return $matches[0];
                    },
                    $content
                );
            }

            if ($content !== $originalContent) {
                $entry['progress'] = $content;
                $updated = true;
                // \Log::info("✓ Updated entry {$index}");
            }
        }

        // Simpan jika ada perubahan
        if ($updated) {
            $this->progress = json_encode($progressData, JSON_PRETTY_PRINT);
            $this->saveQuietly();
            // \Log::info('✓ Progress saved with updated URLs');
        } else {
            // \Log::info('No updates needed');
        }

        // \Log::info('=== END UPDATE URLs ===');
    }

    public static function createTicket(array $validated)
    {
        try {
            $data = [
                'uuid' => Str::uuid()->toString(),
                'ticket_code' => $validated['ticket_code'],
                'ticket_title' => $validated['ticket_title'],
                'ticket_content' => $validated['ticket_content'],
                'ticket_name_client' => $validated['ticket_name_client'],
                'ticket_whatsapp' => $validated['ticket_whatsapp'],
                'ticket_email' => $validated['ticket_email'],
                'ticket_status_uuid' => '631266aa-dcd9-46ca-857b-43128d46edbd', // pending
                'consultant_specialization_uuid' => $validated['consultant_specialization_uuid'],
                'ticket_document_support' => $validated['ticket_document_support']
            ];

            $ticket = self::create($data);
            return [
                'success' => true,
                'message' => 'Ticket berhasil dibuat',
                'data' => $ticket
            ];
        } catch (\Throwable $th) {
            Log::error('Error in createTicket method', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'data' => $validated
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ];
        }
    }

    public static function searchTicket($code_ticket)
    {
        return self::with('ticketStatus')
            ->where('ticket_code', 'like', '%' . $code_ticket . '%')
            ->get();
    }

    public function getRouteKeyName()
    {
        return 'ticket_code';
    }

    public function getTicket()
    {
        // get ticket if member login, else search by ticket_code
    }

    public static function getStat()
    {
        $today = now()->day;
        $chartData = collect(range(1, $today))
            ->map(function ($day) {
                $date = now()->startOfMonth()->addDays($day - 1)->toDateString();
                return self::whereDate('created_at', $date)->count();
            })
            ->toArray();

        $currentCount = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastCount = self::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $percentChange = 0;
        $direction = 'heroicon-m-arrow-trending-up';
        $description = 'No change';

        if ($lastCount > 0) {
            $percentChange = (($currentCount - $lastCount) / $lastCount) * 100;
            $description = number_format(abs($percentChange), 2) . '% ' . ($percentChange >= 0 ? 'increase' : 'decrease');
            $direction = $percentChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down';
        } elseif ($currentCount > 0) {
            $description = 'New tickets this month';
            $direction = 'heroicon-m-arrow-trending-up';
        }

        return [
            'currentCount' => $currentCount,
            'description' => $description,
            'icon' => $direction,
            'color' => $percentChange < 0 ? 'danger' : 'success',
            'chart' => $chartData
        ];
    }


    // relationship
    public function ticketStatus()
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_uuid', 'uuid');
    }

    public function consultantSpecialization()
    {
        return $this->belongsTo(ConsultantSpecialization::class, 'consultant_specialization_uuid', 'uuid');
    }
}
