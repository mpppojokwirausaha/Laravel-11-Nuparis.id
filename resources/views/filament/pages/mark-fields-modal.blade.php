<style>
    .cg-outer {
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }

    .cg-preview-col {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .cg-right-col {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    @media (min-width: 1024px) {
        .cg-outer {
            flex-direction: row;
        }

        .cg-preview-col {
            width: 70%;
            flex-shrink: 0;
            position: sticky;
            top: 0;
        }

        .cg-right-col {
            width: 30%;
        }
    }

    .cg-settings-col {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        width: 100%;
        max-height: 38vh;
        overflow-y: auto;
    }

    @media (min-width: 640px) {
        .cg-settings-col {
            grid-template-columns: 1fr 1fr;
        }
    }

    .cg-card {
        border: 1px solid #374151;
        border-radius: 8px;
        padding: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        background: rgba(255, 255, 255, 0.02);
    }

    .cg-card-title {
        font-weight: 600;
        font-size: 12px;
        color: #e5e7eb;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cg-row {
        display: flex;
        gap: 6px;
        align-items: center;
        font-size: 12px;
        color: #d1d5db;
    }

    .cg-row label {
        width: 32px;
        flex-shrink: 0;
    }

    .cg-row input[type="number"] {
        flex: 1 1 0%;
        min-width: 0;
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 6px;
        color: #e5e7eb;
        font-size: 12px;
        padding: 4px 6px;
    }

    .cg-row .cg-unit {
        flex-shrink: 0;
        color: #9ca3af;
    }

    .cg-row select {
        flex: 1 1 0%;
        min-width: 0;
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 6px;
        color: #e5e7eb;
        font-size: 12px;
        padding: 4px 6px;
    }

    .cg-row input[type="color"] {
        width: 28px;
        height: 24px;
        padding: 0;
        border: 0;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .cg-style-toggles {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    .cg-style-btn {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #4b5563;
        border-radius: 4px;
        background: #1f2937;
        color: #9ca3af;
        font-size: 11px;
        cursor: pointer;
        user-select: none;
    }

    .cg-style-btn.cg-active {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
    }

    .cg-preview-col img {
        max-width: 100%;
        max-height: 65vh;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .cg-manage-del-btn {
        flex-shrink: 0;
        border: 0;
        background: transparent;
        color: #ef4444;
        font-size: 11px;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .cg-manage-del-btn:hover {
        background: rgba(239, 68, 68, 0.15);
    }

    .cg-manage-add-row {
        display: flex;
        gap: 6px;
    }

    .cg-manage-add-row input[type="text"] {
        flex: 1 1 0%;
        min-width: 0;
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 6px;
        color: #e5e7eb;
        font-size: 12px;
        padding: 6px 8px;
    }

    .cg-manage-btn {
        flex-shrink: 0;
        border: 1px solid #4b5563;
        border-radius: 6px;
        background: #1f2937;
        color: #e5e7eb;
        font-size: 12px;
        padding: 6px 10px;
        cursor: pointer;
        white-space: nowrap;
    }

    .cg-manage-btn:hover {
        background: #374151;
    }

    .cg-manage-btn-primary {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
    }

    .cg-manage-btn-primary:hover {
        background: #b91c1c;
    }

    .cg-manage-item-badge {
        font-size: 9px;
        color: #9ca3af;
        border: 1px solid #4b5563;
        border-radius: 4px;
        padding: 1px 5px;
        flex-shrink: 0;
    }

    /* ==== List Field (belum ditempatkan) ==== */
    .cg-list-panel {
        border: 1px solid #374151;
        border-radius: 8px;
        padding: 8px;
        background: rgba(255, 255, 255, 0.02);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cg-list-title {
        font-weight: 600;
        font-size: 12px;
        color: #e5e7eb;
        margin: 0;
    }

    .cg-list-hint {
        font-size: 11px;
        color: #6b7280;
        margin: 0;
    }

    .cg-list-items {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-height: 32px;
    }

    .cg-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 6px 10px;
        border: 1px dashed #4b5563;
        border-radius: 6px;
        background: #1f2937;
        color: #e5e7eb;
        font-size: 12px;
        cursor: grab;
        touch-action: none;
        user-select: none;
    }

    .cg-list-item:active {
        cursor: grabbing;
        opacity: 0.7;
    }

    .cg-list-empty {
        font-size: 11px;
        color: #6b7280;
        font-style: italic;
        padding: 4px 2px;
        margin: 0;
    }

    .cg-ghost {
        position: fixed;
        pointer-events: none;
        z-index: 999;
        background-color: #dc2626;
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 6px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
        white-space: nowrap;
        transform: translate(-50%, -130%);
    }
</style>

<div wire:key="canvas-{{ $record->uuid }}" wire:ignore x-data="{
    templateId: @js($record->uuid),
    fields: @js($fields),
    dragging: null,
    ghostPos: { x: 0, y: 0 },
    pdfWidthPt: @js($record->image_width ?? 0),
    pdfHeightPt: @js($record->image_height ?? 0),
    canvasWidthPx: 0,

    // BARU: field baru/dihapus ditempel langsung ke object fields yang udah
    // ada (bukan reset total), supaya posisi field lain yang lagi di-drag
    // (belum diklik tombol Simpan Posisi) gak ikut ke-reset.
    init() {
        // FIX: SweetAlert2 sebelumnya di-load lewat tag <script src> statis
        // di Blade — tapi modal ini disuntik ke DOM lewat respons AJAX
        // Livewire (bukan render halaman biasa), dan browser TIDAK
        // mengeksekusi tag <script> yang disisipkan lewat cara itu. Makanya
        // `Swal` gak pernah ke-define dan tombol Hapus kelihatan gak
        // ngapa-ngapain. Solusinya: load scriptnya via JS (createElement),
        // ini selalu dieksekusi browser apa pun cara HTML-nya disisipkan.
        if (typeof window.Swal === 'undefined' && !document.getElementById('cg-swal2-script')) {
            const swalScript = document.createElement('script');
            swalScript.id = 'cg-swal2-script';
            swalScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(swalScript);
        }

        window.addEventListener('field-added', (e) => {
            const detail = e.detail[0] ?? e.detail;
            if (detail.templateId !== this.templateId) return;
            this.fields[detail.key] = detail.field;
        });

        window.addEventListener('field-removed', (e) => {
            const detail = e.detail[0] ?? e.detail;
            if (detail.templateId !== this.templateId) return;
            delete this.fields[detail.key];
            if (this.dragging === detail.key) {
                this.dragging = null;
            }
        });
    },

    // Diukur setelah gambar sertifikat SELESAI dimuat (@load), lalu disimpan
    // sebagai state reaktif. Ini penting supaya ukuran font di preview tidak
    // berubah-ubah tergantung kapan Alpine sempat menghitungnya.
    measureCanvas() {
        if (this.$refs.canvasWrap) {
            this.canvasWidthPx = this.$refs.canvasWrap.getBoundingClientRect().width;
        }
    },

    // FIX: cuma klik kiri (primary button) yang boleh mulai drag. Sebelumnya
    // klik kanan (misalnya buat buka 'Inspect') juga memicu startDrag kalau
    // kursor kebetulan ada di atas item List Field, lalu context menu browser
    // 'menelan' event pointerup sehingga dragging nyangkut permanen.
    startDrag(key, e) {
        if (e.button !== 0) return;
        this.dragging = key;
        this.ghostPos = { x: e.clientX, y: e.clientY };
    },

    onMove(clientX, clientY) {
        if (!this.dragging) return;
        this.ghostPos = { x: clientX, y: clientY };

        const field = this.fields[this.dragging];
        if (!field) return;

        // kalau field ini SEDANG di kanvas (placed), marker-nya ikut
        // mengambang mengikuti kursor (live update posisi)
        if (field.is_placed) {
            const rect = this.$refs.canvasWrap.getBoundingClientRect();
            let x = ((clientX - rect.left) / rect.width) * 100;
            let y = ((clientY - rect.top) / rect.height) * 100;
            x = Math.max(0, Math.min(100, x));
            y = Math.max(0, Math.min(100, y));
            field.x = Math.round(x * 100) / 100;
            field.y = Math.round(y * 100) / 100;
        }
        // kalau field ini dari List (belum placed), cukup ghost yang
        // mengikuti kursor; posisi asli ditentukan saat drop (stopDrag)
    },

    stopDrag(clientX, clientY) {
        if (!this.dragging) return;

        const key = this.dragging;
        const field = this.fields[key];
        if (!field) { this.dragging = null; return; }

        const rect = this.$refs.canvasWrap.getBoundingClientRect();

        const insideCanvas = clientX >= rect.left && clientX <= rect.right &&
            clientY >= rect.top && clientY <= rect.bottom;

        if (insideCanvas) {
            let x = ((clientX - rect.left) / rect.width) * 100;
            let y = ((clientY - rect.top) / rect.height) * 100;
            field.x = Math.max(0, Math.min(100, Math.round(x * 100) / 100));
            field.y = Math.max(0, Math.min(100, Math.round(y * 100) / 100));
            field.is_placed = true;
        } else {
            // FIX: reset ke posisi netral saat field dibatalkan penempatannya,
            // supaya tidak nyimpen koordinat 'nyasar' (mis. x:100 karena
            // sempat ke-clamp di tepi kanvas waktu ditarik keluar).
            field.is_placed = false;
            field.x = 50;
            field.y = 50;
        }

        this.dragging = null;
    },

    // FIX: dulu ada dua penulis untuk atribut style (x-show DAN :style)
    // pada elemen yang sama, saling menimpa saat salah satu re-evaluasi
    // (mis. dipicu resize window / buka DevTools) — field yang harusnya
    // is_placed:false bisa 'muncul lagi' di kanvas walau datanya tidak
    // berubah. Sekarang display sepenuhnya dikontrol di sini, satu sumber.
    fieldStyle(key, field) {
        if (!field.is_placed) {
            return 'display:none;';
        }

        if (key === 'qrcode') {
            const widthPercent = this.pdfWidthPt ? (field.font_size / this.pdfWidthPt) * 100 : 8;
            const heightPercent = this.pdfHeightPt ? (field.font_size / this.pdfHeightPt) * 100 : 8;
            return `position:absolute; left:${field.x}%; top:${field.y}%; width:${widthPercent}%; height:${heightPercent}%; transform: translate(-50%, -50%); cursor:grab; touch-action:none; border:2px dashed #dc2626; background-color:rgba(220,38,38,0.15); box-sizing:border-box; display:flex; align-items:center; justify-content:center; text-align:center; line-height:1.1; font-size:10px; font-weight:600; color:#dc2626; user-select:none; z-index:50;`;
        }

        // field teks: placeholder kotak putus-putus transparan, konsisten dengan gaya QR Code.
        // font_size dikonversi dari pt (satuan asli, relatif ke lebar halaman PDF) ke px di layar
        // preview, supaya perubahan angka di input Font benar-benar terlihat di kanvas.
        const ptToPx = (this.pdfWidthPt && this.canvasWidthPx) ? (this.canvasWidthPx / this.pdfWidthPt) : 0.75;
        const previewFontSize = Math.max(8, Math.round(field.font_size * ptToPx));

        const widthCh = Math.max(field.label.length, 6) + 2;
        const fontWeight = field.font_bold ? '700' : '400';
        const textDecoration = field.font_underline ? 'underline' : 'none';

        const cssFontMap = {
            Helvetica: 'Helvetica, Arial, sans-serif',
            Times: 'Times New Roman, Times, serif',
            Courier: 'Courier New, Courier, monospace',
        };
        const cssFontFamily = cssFontMap[field.font_family] || cssFontMap.Helvetica;

        const alignMap = {
            left: 'flex-start',
            center: 'center',
            right: 'flex-end',
        };
        const justify = alignMap[field.text_align] || 'center';
        const textAlign = field.text_align || 'center';

        // FIX: titik (x,y) yang di-drag sekarang jadi TITIK JANGKAR sesuai
        // perataan — bukan selalu titik tengah kotak seperti sebelumnya.
        // Rata kiri => (x,y) ujung kiri teks, rata kanan => (x,y) ujung
        // kanan teks, rata tengah => (x,y) tengah teks (perilaku lama).
        // Ini HARUS sinkron persis dengan perhitungan $textX di
        // CertificateGeneratorService, supaya preview & hasil PDF ketemu.
        const translateXMap = {
            left: '0%',
            center: '-50%',
            right: '-100%',
        };
        const translateX = translateXMap[field.text_align] || '-50%';

        // FIX: border, padding & border-radius pakai satuan 'em' (bukan 'px')
        // supaya otomatis ikut menyusut/membesar proporsional mengikuti
        // font-size (yang sudah dihitung dinamis dari lebar kanvas). Sebelumnya
        // px tetap, jadi waktu sertifikat mengecil (mis. window diperkecil),
        // border+padding jadi kelihatan tidak proporsional/lebih besar.
        return `position:absolute; left:${field.x}%; top:${field.y}%; min-width:${widthCh}ch; transform: translate(${translateX}, -50%); cursor:grab; touch-action:none; border:0.08em dashed #dc2626; background-color:rgba(220,38,38,0.15); box-sizing:border-box; padding:0.3em 0.5em; border-radius:0.3em; display:flex; align-items:center; justify-content:${justify}; text-align:${textAlign}; line-height:1.1; font-family:${cssFontFamily}; font-size:${previewFontSize}px; font-weight:${fontWeight}; text-decoration:${textDecoration}; color:${field.font_color || '#dc2626'}; white-space:nowrap; user-select:none; z-index:50;`;
    },

    hasUnplacedFields() {
        return Object.values(this.fields).some(f => !f.is_placed);
    },

    // Konfirmasi hapus field pakai SweetAlert2 (bukan confirm() bawaan
    // browser). Field yang belum pernah dipakai generate bakal dihapus
    // permanen; yang sudah pernah dipakai bakal diarsipkan (disembunyikan)
    // — keputusan itu ditentukan di server, dialog ini cuma konfirmasi awal.
    confirmDeleteField(field) {
        // Fallback jaga-jaga: kalau CDN SweetAlert2 lambat/gagal ke-load,
        // tetap bisa hapus pakai confirm() bawaan browser daripada tombol
        // Hapus keliatan gak ngapa-ngapain sama sekali.
        if (typeof window.Swal === 'undefined') {
            if (confirm('Yakin mau hapus field &quot;' + field.label + '&quot;?')) {
                $wire.call('deleteOrArchiveField', field.uuid);
            }
            return;
        }

        Swal.fire({
            title: 'Hapus field ini?',
            html: `Field <b>&quot;${field.label}&quot;</b> akan dihapus dari sertifikat. Kalau sudah pernah dipakai generate, field ini akan diarsipkan (disembunyikan), bukan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#374151',
            background: '#1f2937',
            color: '#e5e7eb',
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.call('deleteOrArchiveField', field.uuid);
            }
        });
    },

    async save() {
        await $wire.call('saveFields', this.templateId, this.fields);
    }
}"
    @pointermove.window="onMove($event.clientX, $event.clientY)"
    @pointerup.window="stopDrag($event.clientX, $event.clientY)"
    @pointercancel.window="stopDrag($event.clientX, $event.clientY)" @contextmenu.window="dragging = null"
    @blur.window="dragging = null">

    <p style="margin-bottom:16px; font-size:14px; color:#9ca3af;">
        Geser (drag) kotak merah ke posisi yang diinginkan di atas sertifikat.
        Field yang belum dipakai ada di panel <strong>List Field</strong> di bawah — tinggal drag masuk ke sertifikat
        kalau dibutuhkan. Field yang sudah ada di sertifikat juga bisa di-drag balik ke List untuk membatalkan
        penempatannya.
        Untuk QR code, "Ukuran" berfungsi sebagai sisi kotak (persegi) dalam point (pt).
        Setelah semua posisi pas, klik <strong>Simpan Posisi</strong> di bagian bawah.
    </p>

    @if (empty($fields))
        <div style="color:#f59e0b; font-size:14px; margin-bottom:16px;">
            Belum ada field sama sekali untuk template ini. Tambahkan field dulu lewat panel "List Field" di
            sebelah kanan.
        </div>
    @endif

    <div class="cg-outer">

        {{-- KOLOM 1: preview PDF + drag-drop marker --}}
        <div class="cg-preview-col">
            @if ($previewImageUrl)
                <div x-ref="canvasWrap" x-init="$nextTick(() => measureCanvas())" @resize.window.debounce.150ms="measureCanvas()"
                    style="position:relative; display:inline-block; max-width:100%; border:1px solid #d1d5db; border-radius:8px; overflow:hidden;">
                    <img src="{{ $previewImageUrl }}" style="display:block; max-width:100%; height:auto;"
                        draggable="false" @load="measureCanvas()">
                    <template x-for="(field, key) in fields" :key="key">
                        <div @pointerdown="startDrag(key, $event)" :style="fieldStyle(key, field)"
                            x-text="key === 'qrcode' ? '' : field.label"></div>
                    </template>
                </div>
            @else
                <div style="color:#dc2626; font-size:14px; margin-bottom:16px;">
                    Preview PDF tidak tersedia. Cek apakah Ghostscript (gs) sudah terpasang di server.
                </div>
            @endif
        </div>

        {{-- KOLOM 2: panel setting + List Field (di bawah panel setting) --}}
        <div class="cg-right-col">
            <div class="cg-settings-col">
                <template x-for="(field, key) in fields" :key="key + '-settings'">
                    <div class="cg-card" x-show="field.is_placed">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:4px;">
                            <p class="cg-card-title" x-text="field.label" style="margin:0;"></p>
                            <button type="button" class="cg-manage-del-btn" style="flex-shrink:0;"
                                @click="confirmDeleteField(field)">
                                Hapus
                            </button>
                        </div>

                        <div class="cg-row">
                            <label>X</label>
                            <input type="number" step="0.1" x-model.number="field.x">
                            <span class="cg-unit">%</span>
                        </div>

                        <div class="cg-row">
                            <label>Y</label>
                            <input type="number" step="0.1" x-model.number="field.y">
                            <span class="cg-unit">%</span>
                        </div>

                        <div class="cg-row" x-show="key !== 'qrcode'">
                            <label>Ukuran</label>
                            <input type="number" x-model.number="field.font_size">
                            <span class="cg-unit">pt</span>
                        </div>

                        <div class="cg-row" x-show="key !== 'qrcode'">
                            <label>Font</label>
                            <select x-model="field.font_family">
                                <option value="Helvetica">Helvetica</option>
                                <option value="Times">Times</option>
                                <option value="Courier">Courier</option>
                            </select>
                            <input type="color" x-model="field.font_color">
                        </div>

                        <div class="cg-row" x-show="key !== 'qrcode'">
                            <label>Style</label>
                            <div class="cg-style-toggles">
                                <div class="cg-style-btn" :class="field.font_bold && 'cg-active'"
                                    @click="field.font_bold = !field.font_bold" title="Bold">
                                    <strong>B</strong>
                                </div>
                                <div class="cg-style-btn" :class="field.font_underline && 'cg-active'"
                                    @click="field.font_underline = !field.font_underline" title="Underline">
                                    <span style="text-decoration:underline;">U</span>
                                </div>
                            </div>
                        </div>

                        <div class="cg-row" x-show="key !== 'qrcode'">
                            <label>Rata</label>
                            <div class="cg-style-toggles">
                                <div class="cg-style-btn" :class="field.text_align === 'left' && 'cg-active'"
                                    @click="field.text_align = 'left'" title="Rata Kiri">⯇</div>
                                <div class="cg-style-btn" :class="field.text_align === 'center' && 'cg-active'"
                                    @click="field.text_align = 'center'" title="Rata Tengah">☰</div>
                                <div class="cg-style-btn" :class="field.text_align === 'right' && 'cg-active'"
                                    @click="field.text_align = 'right'" title="Rata Kanan">⯈</div>
                            </div>
                        </div>

                        <div class="cg-row" x-show="key === 'qrcode'">
                            <label>Size</label>
                            <input type="number" x-model.number="field.font_size">
                            <span class="cg-unit">pt</span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- List Field: field yang belum ditaruh di sertifikat, SEKALIGUS
             tempat kelola field (tambah/hapus) --}}
            <div class="cg-list-panel" x-ref="listPanel">
                <p class="cg-list-title">List Field (belum ditempatkan)</p>
                <p class="cg-list-hint">Drag ke sertifikat untuk memakainya. Field yang sudah pernah dipakai
                    generate gak bisa dihapus permanen — cuma diarsipkan.</p>

                <div class="cg-list-items">
                    <template x-for="(field, key) in fields" :key="key + '-list'">
                        <div class="cg-list-item" x-show="!field.is_placed">
                            <span @pointerdown="startDrag(key, $event)" style="cursor:grab; flex:1;"
                                x-text="field.label"></span>
                            <span class="cg-manage-item-badge" x-show="field.usage_count > 0"
                                x-text="field.usage_count + 'x'"
                                title="Sudah dipakai, gak bisa dihapus permanen"></span>
                            <button type="button" class="cg-manage-del-btn" @click="confirmDeleteField(field)">
                                Hapus
                            </button>
                        </div>
                    </template>

                    <p class="cg-list-empty" x-show="Object.keys(fields).length > 0 && !hasUnplacedFields()">
                        Semua field sudah ditempatkan di sertifikat.
                    </p>
                    <p class="cg-list-empty" x-show="Object.keys(fields).length === 0">
                        Belum ada field sama sekali. Tambahkan lewat form di bawah.
                    </p>
                </div>

                {{-- Tambah field baru + tambah QR Code --}}
                <div class="cg-manage-add-row" x-data="{ newLabel: '' }" style="margin-top:4px;">
                    <input type="text" x-model="newLabel" placeholder="Nama field baru, mis. Nama Peserta"
                        @keydown.enter="$wire.call('addCustomField', templateId, newLabel); newLabel = ''">
                    <button type="button" class="cg-manage-btn"
                        @click="$wire.call('addCustomField', templateId, newLabel); newLabel = ''">
                        + Field
                    </button>
                    <button type="button" class="cg-manage-btn cg-manage-btn-primary" x-show="!('qrcode' in fields)"
                        @click="$wire.call('addQrField', templateId)">
                        + QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Ghost: mengikuti kursor/jari saat drag field dari List --}}
    <div class="cg-ghost" x-show="dragging && fields[dragging] && !fields[dragging].is_placed"
        :style="`left:${ghostPos.x}px; top:${ghostPos.y}px;`"
        x-text="dragging && fields[dragging] ? fields[dragging].label : ''"></div>

    <div style="margin-top:24px;">
        <x-filament::button x-on:click="save()">
            Simpan Posisi
        </x-filament::button>
    </div>
</div>
