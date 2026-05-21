<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Storage;

class PartnersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $partners;
    protected $startNumber;

    public function __construct($partners, $startNumber = 1)
    {
        $this->partners = $partners;
        $this->startNumber = $startNumber;
    }

    public function collection()
    {
        return $this->partners;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAME',
            'PHONE',
            'EMAIL',
            'TYPE',
            'URL',
            'LINK NPWP',
            'LINK NIB',
            'CREATED AT'
        ];
    }

    public function map($partner): array
    {
        static $rowNumber = 0;
        if ($rowNumber === 0) {
            $rowNumber = $this->startNumber;
        }

        $currentNumber = $rowNumber;
        $rowNumber++;

        // Generate link untuk NPWP
        $npwpLink = '';
        if ($partner->partner_NPWP) {
            $npwpLink = url(Storage::url($partner->partner_NPWP));
        }

        // Generate link untuk NIB
        $nibLink = '';
        if ($partner->partner_NIB) {
            $nibLink = url(Storage::url($partner->partner_NIB));
        }

        return [
            $currentNumber,
            $partner->partner_name,
            $partner->partner_phone,
            $partner->partner_email,
            is_array($partner->partner_type) ? implode(', ', $partner->partner_type) : $partner->partner_type,
            $partner->partner_url,
            $npwpLink,
            $nibLink,
            $partner->created_at,
        ];
    }
}
