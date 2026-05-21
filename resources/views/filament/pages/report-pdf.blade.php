{{-- RIWAYAT PROGRESS --}}
@if (!empty($progressDocs))
    <div class="tp-section-label progress {{ !empty($clientDocs) ? 'has-top' : '' }}">Riwayat Progress</div>
    
    @foreach ($progressDocs as $idx => $doc)
        @php
            $isLatest = $idx === count($progressDocs) - 1;
            $rawText = $doc['text'] ?? '';
            $htmlContent = is_string($rawText) ? $rawText : '';
            
            // ============================================================
            // 1. HAPUS SEMUA TAG GAMBAR DAN STRUKTUR CAPTION
            // ============================================================
            $cleanHtml = preg_replace('/<img[^>]+>/i', '', $htmlContent);
            $cleanHtml = preg_replace('/<figure[^>]*data-trix-attachment[^>]*>.*?<\/figure>/s', '', $cleanHtml);
            $cleanHtml = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/s', '', $cleanHtml);
            $cleanHtml = preg_replace('/<div class="attachment-gallery[^>]*">.*?<\/div>/s', '', $cleanHtml);
            $cleanHtml = preg_replace('/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is', '', $cleanHtml);
            $cleanHtml = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $cleanHtml);
            $cleanHtml = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $cleanHtml);
            
            // ============================================================
            // 2. HAPUS CAPTION (TEKS PENDEK YANG TIDAK PENTING)
            // ============================================================
            $lines = explode("\n", $cleanHtml);
            $filteredLines = [];
            
            // Kata kunci caption (akan dihapus jika menjadi awal kalimat)
            $captionStartWords = [
                'Konfirmasi', 'Sudah', 'Pencabutan', 'Status', 'Valid', 
                'Permohonan', 'disetujui', 'Kami', 'Seluruh', 'Apabila', 
                'Demikian', 'Dengan', 'Kegiatan', 'Buku', 'Rekening', 'Kode'
            ];
            
            foreach ($lines as $line) {
                $trimmed = trim(strip_tags($line));
                if (empty($trimmed)) continue;
                
                $shouldKeep = true;
                
                // CEK 1: Apakah teks diawali kata kunci caption?
                foreach ($captionStartWords as $word) {
                    if (preg_match('/^' . preg_quote($word, '/') . '/i', $trimmed)) {
                        // KECUALI jika teksnya panjang (>60 karakter) atau mengandung angka
                        if (strlen($trimmed) <= 60 && !preg_match('/[0-9]/', $trimmed)) {
                            $shouldKeep = false;
                            break;
                        }
                    }
                }
                
                // CEK 2: Apakah teks terlalu pendek (<30 karakter) dan tidak mengandung angka?
                if ($shouldKeep && strlen($trimmed) < 30 && !preg_match('/[0-9]/', $trimmed)) {
                    $shouldKeep = false;
                }
                
                // CEK 3: Apakah teks hanya 1-4 kata dan tidak mengandung angka?
                if ($shouldKeep && str_word_count($trimmed) <= 4 && !preg_match('/[0-9]/', $trimmed)) {
                    $shouldKeep = false;
                }
                
                // CEK 4: Apakah teks adalah list nomor (1., 2., dll) -> WAJIB TAMPIL
                if (preg_match('/^\d+\./', $trimmed)) {
                    $shouldKeep = true;
                }
                
                // CEK 5: Apakah teks mengandung angka (data penting) -> WAJIB TAMPIL
                if (preg_match('/[0-9]/', $trimmed)) {
                    $shouldKeep = true;
                }
                
                if ($shouldKeep) {
                    $filteredLines[] = $line;
                }
            }
            
            $cleanHtml = implode("\n", $filteredLines);
            $cleanHtml = trim($cleanHtml);
            
            // ============================================================
            // 3. KUMPULKAN SEMUA GAMBAR UNTUK THUMBNAIL GRID
            // ============================================================
            $allThumbnails = [];
            
            // Dari thumbnail_files
            foreach ($doc['thumbnail_files'] ?? [] as $tf) {
                if (is_array($tf) && !empty($tf['url'])) {
                    $allThumbnails[] = $tf['url'];
                } elseif (is_string($tf) && !empty($tf)) {
                    $allThumbnails[] = $tf;
                }
            }
            
            // Dari embedded_images
            foreach ($doc['embedded_images'] ?? [] as $img) {
                if (is_array($img) && !empty($img['url'])) {
                    $allThumbnails[] = $img['url'];
                } elseif (is_string($img) && !empty($img)) {
                    $allThumbnails[] = $img;
                }
            }
            
            // Dari file (lampiran langsung)
            foreach ($doc['file'] ?? [] as $file) {
                if (is_string($file) && !empty($file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                        // Untuk PDF, gunakan base64
                        $path = storage_path('app/public/' . $file);
                        if (file_exists($path)) {
                            $base64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path));
                            $allThumbnails[] = $base64;
                        } else {
                            $allThumbnails[] = asset('storage/' . $file);
                        }
                    }
                }
            }
            
            $allThumbnails = array_unique($allThumbnails);
            
            // ============================================================
            // 4. PDF FILES
            // ============================================================
            $pdfFiles = [];
            foreach ($doc['pdf_files'] ?? [] as $pf) {
                if (is_array($pf)) {
                    $pdfFiles[] = ['url' => $pf['url'] ?? '#', 'name' => $pf['name'] ?? 'Document.pdf'];
                } elseif (is_string($pf) && !empty($pf)) {
                    $pdfFiles[] = ['url' => $pf, 'name' => basename($pf)];
                }
            }
            
            // ============================================================
            // 5. OTHER FILES
            // ============================================================
            $otherFiles = [];
            foreach ($doc['other_files'] ?? [] as $lf) {
                if (is_array($lf)) {
                    $otherFiles[] = ['url' => $lf['url'] ?? '#', 'name' => $lf['name'] ?? 'File', 'ext' => $lf['ext'] ?? ''];
                } elseif (is_string($lf) && !empty($lf)) {
                    $otherFiles[] = ['url' => $lf, 'name' => basename($lf), 'ext' => pathinfo($lf, PATHINFO_EXTENSION)];
                }
            }
        @endphp
        
        <div class="tp-entry">
            <div class="tp-meta">
                <span class="tp-badge {{ $isLatest ? 'is-latest' : '' }}">
                    {{ $isLatest ? 'Terbaru' : 'Sebelumnya' }}
                </span>
                <span class="tp-time">
                    {{ \Carbon\Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}
                </span>
            </div>
            
            {{-- TEKS YANG SUDAH BERSIH DARI CAPTION --}}
            @if (!empty($cleanHtml))
                <div class="tp-body">{!! nl2br(e($cleanHtml)) !!}</div>
            @endif
            
            {{-- THUMBNAIL GRID (GAMBAR TAMPIL DI SINI) --}}
            @if (!empty($allThumbnails))
                <div class="tp-thumb-grid">
                    @foreach ($allThumbnails as $thumbUrl)
                        <div class="tp-thumb">
                            <img src="{{ $thumbUrl }}" style="width:100%; height:auto; border:1px solid #ddd; border-radius:4px; margin:5px 0;">
                        </div>
                    @endforeach
                </div>
            @endif
            
            {{-- PDF FILES --}}
            @if (!empty($pdfFiles))
                <div class="tp-files">
                    @foreach ($pdfFiles as $pf)
                        <div class="tp-file-row pdf">
                            <span class="tp-file-ext">PDF</span>
                            <span class="tp-file-name">{{ $pf['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            
            {{-- OTHER FILES --}}
            @if (!empty($otherFiles))
                <div class="tp-files">
                    @foreach ($otherFiles as $lf)
                        <div class="tp-file-row">
                            <span class="tp-file-ext">{{ strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3)) }}</span>
                            <span class="tp-file-name">{{ $lf['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
@endif