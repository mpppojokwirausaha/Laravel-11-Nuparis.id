@php
    use App\Models\Ticket;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // Ambil kode tiket dari route
    $recordId = request()->route('record'); // = ticket_code
    $ticket = Ticket::where('ticket_code', $recordId)->first();
    $progress = $ticket?->ticket_progress ?? [];
    
    // Helper function untuk format ukuran file
    if (!function_exists('formatFileSize')) {
        function formatFileSize($bytes) {
            if ($bytes == 0) return '0 B';
            
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }
    }
    
    // Helper function untuk cek apakah file kecil (kurang dari 1MB)
    if (!function_exists('isSmallFile')) {
        function isSmallFile($size) {
            return $size < 1048576; // 1MB = 1048576 bytes
        }
    }
@endphp

<div class="space-y-4">
    @forelse($progress as $item)
        <div class="p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            @if (!empty($item['progress']))
                @php
                    // Ekstrak semua URL gambar dari progress
                    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $item['progress'], $matches);
                    $imageUrls = $matches[1] ?? [];

                    // Hapus semua tag <img> dari progress
                    $textOnly = preg_replace('/<img[^>]+>/i', '', $item['progress']);

                    // Hapus HANYA tag <a> yang href-nya mengarah ke file gambar (jpg, png, dll)
                    $textOnly = preg_replace(
                        '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                        '',
                        $textOnly,
                    );

                    // Hapus text yang berisi nama file gambar dengan ukuran (contoh: image.png 1.22 MB)
                    $textOnly = preg_replace(
                        '/[\w\-\.]+\.(jpg|jpeg|png|gif|webp|svg|bmp)\s*[\d\.,]+\s*(B|KB|MB|GB|TB)/i',
                        '',
                        $textOnly,
                    );

                    // Hapus <p> kosong atau hanya spasi
                    $textOnly = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $textOnly);

                    // Hapus whitespace berlebih
                    $textOnly = preg_replace('/\s+/', ' ', $textOnly);
                    $textOnly = trim($textOnly);
                @endphp

                @if (trim(strip_tags($textOnly)))
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100 mb-3">
                        {!! $textOnly !!}
                    </div>
                @endif
            @endif
            
            @if (!empty($item['file']) || !empty($imageUrls))
                <div class="mb-3">
                    {{-- THUMBNAIL SECTION (Video dan Gambar Kecil saja) --}}
                    @if (!empty($item['file']))
                        @php
                            $thumbnailFiles = [];
                            $otherFiles = [];
                            
                            foreach ($item['file'] as $file) {
                                $ext = Str::lower(pathinfo($file, PATHINFO_EXTENSION));
                                $fileSize = Storage::exists('public/' . $file) ? Storage::size('public/' . $file) : 0;
                                
                                $videoExt = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv'];
                                $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                                
                                if (in_array($ext, $videoExt)) {
                                    // Video selalu tampil thumbnail
                                    $thumbnailFiles[] = $file;
                                } elseif (in_array($ext, $imageExt) && isSmallFile($fileSize)) {
                                    // Gambar hanya tampil thumbnail jika kecil (< 1MB)
                                    $thumbnailFiles[] = $file;
                                } else {
                                    // File lainnya masuk ke list
                                    $otherFiles[] = $file;
                                }
                            }
                        @endphp
                        
                        @if (!empty($thumbnailFiles))
                            <div class="flex flex-wrap gap-3 mb-3">
                                @foreach ($thumbnailFiles as $file)
                                    @php
                                        $ext = Str::lower(pathinfo($file, PATHINFO_EXTENSION));
                                        $fileUrl = asset('storage/' . $file);
                                        $fileName = basename($file);
                                        $fileSize = Storage::exists('public/' . $file) ? Storage::size('public/' . $file) : 0;
                                        $fileSizeFormatted = $fileSize > 0 ? formatFileSize($fileSize) : '';
                                        
                                        $videoExt = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv'];
                                        $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                                    @endphp
                                    
                                    @if (in_array($ext, $videoExt))
                                        {{-- Thumbnail Video --}}
                                        <div class="thumbnail-item video-thumbnail group"
                                            onclick="openLightbox('{{ $fileUrl }}', 'video')">
                                            <video src="{{ $fileUrl }}" muted preload="metadata"></video>
                                            <div class="play-icon">
                                                <svg class="w-8 h-8" fill="white" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                            <div class="thumbnail-caption">
                                                <span class="text-xs truncate">{{ $fileName }}</span>
                                                @if($fileSizeFormatted)
                                                    <span class="text-xs opacity-75">{{ $fileSizeFormatted }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif (in_array($ext, $imageExt))
                                        {{-- Thumbnail Gambar Kecil --}}
                                        <div class="thumbnail-item group" onclick="openLightbox('{{ $fileUrl }}', 'image')">
                                            <img src="{{ $fileUrl }}" alt="{{ $fileName }}" loading="lazy">
                                            <div class="thumbnail-caption">
                                                <span class="text-xs truncate">{{ $fileName }}</span>
                                                @if($fileSizeFormatted)
                                                    <span class="text-xs opacity-75">{{ $fileSizeFormatted }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- FILE LIST SECTION (File lainnya sebagai link) --}}
                        @if (!empty($otherFiles))
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    📁 File terlampir:
                                </p>
                                <div class="space-y-2">
                                    @foreach ($otherFiles as $file)
                                        @php
                                            $ext = Str::lower(pathinfo($file, PATHINFO_EXTENSION));
                                            $fileUrl = asset('storage/' . $file);
                                            $fileName = basename($file);
                                            $fileSize = Storage::exists('public/' . $file) ? Storage::size('public/' . $file) : 0;
                                            $fileSizeFormatted = $fileSize > 0 ? formatFileSize($fileSize) : '';
                                            
                                            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                                            $pdfExt = ['pdf'];
                                            $docExt = ['doc', 'docx'];
                                            $excelExt = ['xls', 'xlsx', 'csv'];
                                            $archiveExt = ['zip', 'rar', '7z', 'tar', 'gz'];
                                            $audioExt = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
                                        @endphp
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            <div class="flex items-center space-x-3">
                                                @if (in_array($ext, $imageExt))
                                                    {{-- Gambar Besar --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-blue-500">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @elseif (in_array($ext, $pdfExt))
                                                    {{-- PDF --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-red-500">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 8V3.5L18.5 9H13zm-2 2h2v2h-2v-2zm0 4h2v2h-2v-2zm-4-4h2v2H7v-2zm0 4h2v2H7v-2z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-sm font-medium text-red-600 dark:text-red-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @elseif (in_array($ext, $docExt))
                                                    {{-- Word --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-blue-600">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 8V3.5L18.5 9H13zm-3 4h2v2h-2v-2zm0 4h2v2h-2v-2zm-4-4h2v2H7v-2zm0 4h2v2H7v-2z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @elseif (in_array($ext, $excelExt))
                                                    {{-- Excel --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-green-600">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 8V3.5L18.5 9H13zm-5 4h6v2h-6v-2zm6 2v2h-6v-2h6z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-sm font-medium text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @elseif (in_array($ext, $archiveExt))
                                                    {{-- Archive --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-purple-600">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" download class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                @elseif (in_array($ext, $audioExt))
                                                    {{-- Audio --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-yellow-600">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" class="text-sm font-medium text-yellow-600 dark:text-yellow-400 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                    </a>
                                                @else
                                                    {{-- File lainnya --}}
                                                    <div class="w-8 h-8 flex items-center justify-center text-gray-500">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"/>
                                                        </svg>
                                                    </div>
                                                    <a href="{{ $fileUrl }}" download class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:underline flex items-center gap-1">
                                                        {{ $fileName }}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                            @if($fileSizeFormatted)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $fileSizeFormatted }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    {{-- Gambar dari dalam 'progress' (rich text editor) --}}
                    @if (!empty($imageUrls))
                        <div class="flex flex-wrap gap-3 mb-3">
                            @foreach ($imageUrls as $imgUrl)
                                <div class="thumbnail-item group" onclick="openLightbox('{{ $imgUrl }}', 'image')">
                                    <img src="{{ $imgUrl }}" alt="Image" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
            
            {{-- TIMESTAMP --}}
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                📅 {{ \Carbon\Carbon::parse($item['timestamp'])->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
    @empty
        <div class="italic text-gray-500 dark:text-gray-400 text-sm">
            Belum ada progress
        </div>
    @endforelse
</div>

{{-- LIGHTBOX MODAL --}}
<div id="lightbox" class="lightbox-hidden" onclick="handleLightboxClick(event)">
    <div class="lightbox-container">
        <button type="button" onclick="closeLightbox()" class="lightbox-close">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <img id="lightbox-img" src="" alt="Preview" class="lightbox-hidden">
        <video id="lightbox-video" controls class="lightbox-hidden"></video>
    </div>
</div>

<style>
    /* Thumbnail Styles */
    .thumbnail-item {
        flex-shrink: 0;
        cursor: pointer;
        overflow: hidden;
        border-radius: 0.75rem;
        width: 140px;
        position: relative;
        background: #f9fafb;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .dark .thumbnail-item {
        background: #1f2937;
        border-color: #374151;
    }

    .thumbnail-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: #60a5fa;
    }

    .dark .thumbnail-item:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        border-color: #3b82f6;
    }

    .thumbnail-item img,
    .thumbnail-item video {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .thumbnail-item img,
    .dark .thumbnail-item video {
        border-bottom-color: #374151;
    }

    /* Thumbnail Caption */
    .thumbnail-caption {
        padding: 0.5rem;
        width: 100%;
        text-align: center;
        background: rgba(255, 255, 255, 0.95);
    }

    .dark .thumbnail-caption {
        background: rgba(31, 41, 55, 0.95);
    }

    .thumbnail-caption span {
        display: block;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    /* Video Play Icon */
    .video-thumbnail .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        background-color: rgba(0, 0, 0, 0.6);
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .video-thumbnail:hover .play-icon {
        background-color: rgba(234, 179, 8, 0.9);
        transform: translate(-50%, -50%) scale(1.15);
    }

    .video-thumbnail .play-icon svg {
        margin-left: 3px;
    }

    /* File List Styles */
    .file-list-item {
        transition: all 0.2s ease;
    }

    .file-list-item:hover {
        background-color: #f3f4f6;
    }

    .dark .file-list-item:hover {
        background-color: #374151;
    }

    /* Lightbox Styles */
    #lightbox {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background-color: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .lightbox-hidden {
        display: none !important;
    }

    .lightbox-container {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
    }

    .lightbox-close {
        position: absolute;
        top: -3rem;
        right: 0;
        color: white;
        background: none;
        border: none;
        cursor: pointer;
        transition: opacity 0.2s;
        z-index: 10000;
    }

    .lightbox-close:hover {
        opacity: 0.7;
    }

    #lightbox-img,
    #lightbox-video {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }

    #lightbox-img.lightbox-show,
    #lightbox-video.lightbox-show {
        transform: scale(1);
    }
</style>

<script>
    function openLightbox(fileUrl, type) {
        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const video = document.getElementById('lightbox-video');

        if (type === 'image') {
            img.src = fileUrl;
            img.classList.remove('lightbox-hidden');
            video.classList.add('lightbox-hidden');
            video.pause();
            video.src = '';
        } else if (type === 'video') {
            video.src = fileUrl;
            video.classList.remove('lightbox-hidden');
            img.classList.add('lightbox-hidden');
            img.src = '';
        }

        lightbox.classList.remove('lightbox-hidden');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (type === 'image') {
                img.classList.add('lightbox-show');
            } else {
                video.classList.add('lightbox-show');
            }
        }, 10);
    }

    function handleLightboxClick(event) {
        if (event.target.id === 'lightbox') {
            closeLightbox();
        }
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const video = document.getElementById('lightbox-video');

        img.classList.remove('lightbox-show');
        video.classList.remove('lightbox-show');

        if (video && !video.paused) {
            video.pause();
        }

        setTimeout(() => {
            lightbox.classList.add('lightbox-hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });
</script>