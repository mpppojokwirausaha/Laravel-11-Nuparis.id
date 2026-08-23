<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CertificateTemplate extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'background_image',
        'image_width',
        'image_height',
    ];

    // NOTE: AVAILABLE_FIELDS constant DIHAPUS. Field sekarang custom per-template,
    // dibuat lewat "Kelola Field" (panel di modal "Atur Posisi"). Cuma "qrcode"
    // yang statusnya reserved/wajib-tersedia, di-seed otomatis di bawah.
    public const RESERVED_QRCODE_KEY = 'qrcode';

    protected static function booted(): void
    {
        static::saving(function (CertificateTemplate $template) {
            if ($template->isDirty('background_image') && $template->background_image) {
                $fullPath = Storage::disk('public')->path($template->background_image);

                if (file_exists($fullPath)) {
                    $pdf = new \setasign\Fpdi\Fpdi('P', 'pt');
                    $pdf->setSourceFile($fullPath);
                    $templateId = $pdf->importPage(1);
                    $size = $pdf->getTemplateSize($templateId);

                    $template->image_width = (int) round($size['width']);
                    $template->image_height = (int) round($size['height']);
                }
            }
        });

        // Tiap template BARU otomatis punya 1 field "qrcode" (reserved) siap
        // dipakai — field lain (custom) HARUS ditambahkan manual lewat
        // "Kelola Field", gak ada bawaan lagi selain qrcode.
        static::created(function (CertificateTemplate $template) {
            $template->fields()->create([
                'field_key' => self::RESERVED_QRCODE_KEY,
                'label' => 'QR Code',
                'x' => 50,
                'y' => 50,
                'font_size' => 80,
                'font_color' => '#000000',
                'font_family' => 'Helvetica',
                'font_bold' => false,
                'font_underline' => false,
                'text_align' => 'center',
                'is_placed' => false,
                'usage_count' => 0,
                'is_archived' => false,
            ]);
        });
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CertificateTemplateField::class, 'certificate_template_id', 'uuid');
    }

    public function generates(): HasMany
    {
        return $this->hasMany(CertificateGenerate::class, 'certificate_template_id', 'uuid');
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image
            ? Storage::disk('public')->url($this->background_image)
            : null;
    }
}
