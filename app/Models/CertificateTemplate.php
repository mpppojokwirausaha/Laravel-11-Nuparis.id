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

    public const AVAILABLE_FIELDS = [
        'nama' => 'Nama',
        'keterangan' => 'Keterangan',
        'tempat' => 'Tempat',
        'tanggal' => 'Tanggal',
        'tahun' => 'Tahun',
        'qrcode' => 'QR Code',
    ];

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

        static::created(function (CertificateTemplate $template) {
            foreach (self::AVAILABLE_FIELDS as $key => $label) {
                $template->fields()->create([
                    'field_key' => $key,
                    'label' => $label,
                    'x' => 50,
                    'y' => 50,
                    'font_size' => $key === 'qrcode' ? 80 : 24,
                    'font_color' => '#000000',
                    'font_family' => 'Helvetica',
                    'font_bold' => false,
                    'font_underline' => false,
                    'text_align' => 'center',
                    'is_placed' => false,
                ]);
            }
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
