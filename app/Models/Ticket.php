<?php

namespace App\Models;

use App\Models\ConsultantSpecialization;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
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
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
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
