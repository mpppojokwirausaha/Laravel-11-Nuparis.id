<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasCleanExcerpt
{
    protected static function cleanExcerpt(?string $html, int $limit = 200): string
    {
        if (!$html) {
            return '';
        }

        $spaced = preg_replace('/<\/(p|div|h[1-6]|li|br|tr|blockquote)>/i', '$0 ', $html);
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($spaced)));

        return Str::limit($text, $limit);
    }
}
