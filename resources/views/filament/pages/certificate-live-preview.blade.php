@php
    $fontFamilyMap = [
        'Helvetica' => 'Helvetica, Arial, sans-serif',
        'Times' => "'Times New Roman', Times, serif",
        'Courier' => "'Courier New', Courier, monospace",
    ];
@endphp

<div x-data="{
    canvasWidthPx: 0,
    pdfWidthPt: @js($pdfWidthPt ?? 0),

    measure() {
        if (this.$refs.previewImg) {
            this.canvasWidthPx = this.$refs.previewImg.getBoundingClientRect().width;
        }
    },

    ptToPx(fontSizePt) {
        const ratio = (this.pdfWidthPt && this.canvasWidthPx) ? (this.canvasWidthPx / this.pdfWidthPt) : 0.75;
        return Math.max(8, Math.round(fontSizePt * ratio));
    }
}" x-init="$nextTick(() => measure())" @resize.window.debounce.150ms="measure()"
    style="position:relative; display:inline-block; width:100%; max-width:600px; border:1px solid #374151; border-radius:8px; overflow:hidden; background:#111827;">

    @if ($previewImageUrl)
        <img x-ref="previewImg" src="{{ $previewImageUrl }}" style="display:block; width:100%; height:auto;"
            draggable="false" @load="measure()">

        @foreach ($fields as $key => $field)
            @continue($key === 'qrcode')

            @php
                $value = $values[$key] ?? '';
            @endphp

            @continue($value === '')

            @php
                $justify = match ($field->text_align) {
                    'left' => 'flex-start',
                    'right' => 'flex-end',
                    default => 'center',
                };
                $fontFamily = $fontFamilyMap[$field->font_family] ?? $fontFamilyMap['Helvetica'];
            @endphp

            <div
                :style="`position:absolute; left:{{ $field->x }}%; top:{{ $field->y }}%; transform:translate(-50%, -50%); max-width:90%; display:flex; justify-content:{{ $justify }}; font-family:{{ $fontFamily }}; font-size:${ptToPx({{ (float) $field->font_size }})}px; font-weight:{{ $field->font_bold ? '700' : '400' }}; text-decoration:{{ $field->font_underline ? 'underline' : 'none' }}; color:{{ $field->font_color }}; text-align:{{ $field->text_align }}; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; pointer-events:none;`">
                {{ $value }}</div>
        @endforeach

        @php
            $qrField = $fields->get('qrcode');
        @endphp

        @if ($qrField)
            @php
                $qrSidePercent = $pdfWidthPt ? ($qrField->font_size / $pdfWidthPt) * 100 : 8;
            @endphp
            <div
                style="
                position:absolute;
                left:{{ $qrField->x }}%;
                top:{{ $qrField->y }}%;
                width:{{ $qrSidePercent }}%;
                aspect-ratio:1/1;
                transform:translate(-50%, -50%);
                border:2px dashed #9ca3af;
                background:rgba(255,255,255,0.08);
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:9px;
                color:#9ca3af;
                pointer-events:none;
            ">
                QR</div>
        @endif
    @else
        <div style="padding:48px 16px; color:#9ca3af; font-size:13px; text-align:center;">
            Pilih template di sebelah kiri untuk melihat preview sertifikat.
        </div>
    @endif
</div>
