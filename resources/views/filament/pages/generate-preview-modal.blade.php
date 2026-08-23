<div style="display:flex; flex-direction:column; gap:16px; align-items:center;">
    @if ($previewImageUrl)
        <img src="{{ $previewImageUrl }}"
            style="max-width:100%; max-height:65vh; border:1px solid #d1d5db; border-radius:8px; object-fit:contain;"
            alt="Preview Sertifikat">
    @else
        <div style="color:#9ca3af; font-size:14px; text-align:center; padding:24px 0;">
            Preview tidak tersedia untuk file ini — kemungkinan hasilnya berupa arsip (ZIP) berisi banyak
            sertifikat, atau Ghostscript belum terpasang di server. File tetap bisa langsung diunduh di
            bawah.
        </div>
    @endif

    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:center;">
        <x-filament::button tag="a" href="{{ $record->file_url }}" target="_blank" icon="heroicon-o-arrow-down-tray">
            Unduh Sertifikat
        </x-filament::button>
    </div>
</div>
