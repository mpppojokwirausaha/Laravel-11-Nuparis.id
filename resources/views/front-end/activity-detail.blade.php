<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Tags -->
    <meta name="title" content="{{ $activity->activity_title }}" />
    <meta name="description" content="{{ $activity->seoDescription }}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $activity->activity_title }}" />
    <meta property="og:description"
        content="{{ $activity->excerpt ?: strip_tags(Str::limit($activity->content, 160)) }}" />
    <meta property="og:image"
        content="{{ $activity->activity_image ? asset('storage/' . $activity->activity_image) : asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:site_name" content="NUPARIS" />
    <meta property="article:published_time" content="{{ $activity->created_at->toIso8601String() }}" />
    <meta property="article:author" content="Tim Redaksi NUPARIS" />
    <meta property="article:section" content="Aktivitas" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="{{ $activity->activity_title }}" />
    <meta property="twitter:description"
        content="{{ $activity->excerpt ?: strip_tags(Str::limit($activity->content, 160)) }}" />
    <meta property="twitter:image"
        content="{{ $activity->activity_image ? asset('storage/' . $activity->activity_image) : asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="{{ $activity->activity_title }}" />
    <meta property="linkedin:description"
        content="{{ $activity->excerpt ?: strip_tags(Str::limit($activity->content, 160)) }}" />
    <meta property="linkedin:image"
        content="{{ $activity->activity_image ? asset('storage/' . $activity->activity_image) : asset('storage/' . $infos->meta_image) }}" />

    <!-- Tailwind Configuration -->
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans bg-slate-50 text-gray-800 min-h-screen">

    @include('front-end.layouts.components.header')

    <!-- Floating Share Button -->
    <button id="floatingShareBtn"
        class="hidden lg:flex items-center justify-center w-14 h-14 bg-primary text-white rounded-full hover:bg-red-700 transition-colors fixed right-5 bottom-10 z-40 shadow-lg">
        <i class="fas fa-share-alt text-xl"></i>
    </button>

    <!-- Main Content -->
    <main class="pt-8 lg:pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-slate-600">
                <ol class="flex flex-wrap items-center space-x-2">
                    <li><a href="{{ route('landingpage') }}" class="hover:text-primary transition-colors">Beranda</a>
                    </li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('activity-more') }}"
                            class="hover:text-primary transition-colors">Aktivitas</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-slate-800 font-medium">{{ $activity->activity_title }}</li>
                </ol>
            </nav>

            <!-- Activity Container -->
            <article class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden mb-8">
                <!-- Activity Hero -->
                <div class="relative h-64 sm:h-80 lg:h-96 overflow-hidden">
                    <img src="{{ $activity->activity_image ? asset('storage/' . $activity->activity_image) : 'https://picsum.photos/seed/aktivitas-detail/1200/600' }}"
                        alt="{{ $activity->activity_title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <!-- Logo -->
                    <div class="absolute top-4 left-4 z-20 opacity-80">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <img src="{{ asset('storage/' . $infos->meta_image) }}" alt="NUPARIS Logo" class="w-8 h-8">
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="absolute top-4 right-4 z-20">
                        <span
                            class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">Aktivitas</span>
                    </div>

                    <!-- Activity Info -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 pt-16 text-white">
                        <div class="flex items-center space-x-4 mb-3 text-sm">
                            <span class="flex items-center space-x-1">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ $activity->created_at->translatedFormat('d M Y') }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <i class="far fa-clock"></i>
                                <span>{{ $activity->created_at->translatedFormat('H:i') }} WIB</span>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight mb-4">
                            {{ $activity->activity_title }}</h1>
                        <div class="hidden md:flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white">
                                <img src="{{ asset('storage/' . $infos->meta_image) }}" alt="Penulis"
                                    class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium">Tim Redaksi NUPARIS</p>
                                <p class="text-sm opacity-90">NUPARIS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Meta -->
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-eye"></i>
                                <span class="text-sm font-medium">{{ $activity->views ?? 0 }} <span
                                        class="hidden lg:inline">views</span></span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-comment"></i>
                                <span class="text-sm font-medium">{{ $activity->comments_count ?? 0 }} <span
                                        class="hidden lg:inline">komentar</span></span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-share-square"></i>
                                <span class="text-sm font-medium" id="shareCount">{{ $activity->shares_count ?? 0 }}
                                    <span class="hidden lg:inline">shares</span></span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button data-platform="linkedin"
                                class="w-10 h-10 rounded-full bg-[#0A66C2] text-white hover:bg-[#004182] transition-colors flex items-center justify-center">
                                <i class="fab fa-linkedin-in"></i>
                            </button>
                            <button data-platform="whatsapp"
                                class="w-10 h-10 rounded-full bg-[#25D366] text-white hover:bg-[#22C35E] transition-colors flex items-center justify-center">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button id="copyLinkBtnMain"
                                class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors flex items-center justify-center">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Activity Content -->
                <div class="p-6 sm:p-8 lg:p-8">
                    <div
                        class="prose prose-lg max-w-none prose-headings:text-slate-800 prose-headings:font-bold prose-p:text-slate-600 prose-p:leading-relaxed prose-ul:list-disc prose-ol:list-decimal prose-a:text-primary hover:prose-a:underline prose-img:rounded-lg prose-img:w-full">
                        {!! $activity->cleaned_content !!}
                    </div>
                </div>
            </article>
        </div>
    </main>

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 z-[10000] hidden">
        <div class="absolute inset-0 bg-black/50" id="shareModalBackdrop"></div>
        <div
            class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto animate-modal-in">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Bagikan Aktivitas</h3>
                    <p class="text-slate-600 text-sm mt-1">Bagikan aktivitas ini melalui</p>
                </div>
                <button id="closeShareModal" class="text-slate-400 hover:text-slate-800 p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-6">
                <button data-platform="facebook"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#1877F2] text-white hover:bg-[#166FE5] transition-colors">
                    <i class="fab fa-facebook-f text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Facebook</span>
                </button>
                <button data-platform="twitter"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#1DA1F2] text-white hover:bg-[#1A8CD8] transition-colors">
                    <i class="fab fa-twitter text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Twitter</span>
                </button>
                <button data-platform="whatsapp"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#25D366] text-white hover:bg-[#22C35E] transition-colors">
                    <i class="fab fa-whatsapp text-2xl mb-2"></i>
                    <span class="text-xs font-medium">WhatsApp</span>
                </button>
                <button data-platform="telegram"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#0088CC] text-white hover:bg-[#0077B5] transition-colors">
                    <i class="fab fa-telegram text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Telegram</span>
                </button>
                <button data-platform="linkedin"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#0A66C2] text-white hover:bg-[#004182] transition-colors">
                    <i class="fab fa-linkedin-in text-2xl mb-2"></i>
                    <span class="text-xs font-medium">LinkedIn</span>
                </button>
                <button data-platform="email"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-[#EA4335] text-white hover:bg-[#D32F2F] transition-colors">
                    <i class="fas fa-envelope text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Email</span>
                </button>
                <button id="copyLinkBtn"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors">
                    <i class="fas fa-link text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Copy Link</span>
                </button>
                <button id="moreOptionsBtn"
                    class="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                    <i class="fas fa-ellipsis-h text-2xl mb-2"></i>
                    <span class="text-xs font-medium">Lainnya</span>
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Tautan Aktivitas</label>
                <div class="flex">
                    <input type="text" id="shareUrl" value="{{ url()->current() }}" readonly
                        class="flex-1 px-4 py-3 bg-slate-100 border border-slate-300 rounded-l-lg text-slate-600 focus:outline-none">
                    <button id="copyUrlBtn"
                        class="px-4 py-3 bg-primary text-white font-medium rounded-r-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-copy mr-2"></i>Copy
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Pesan (opsional)</label>
                <textarea id="shareMessage" rows="3" placeholder="Tambahkan pesan..."
                    class="w-full px-4 py-3 bg-slate-100 border border-slate-300 rounded-lg text-slate-600 focus:outline-none"></textarea>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="hidden fixed top-5 right-5 z-50 animate-slide-up">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle"></i>
                <span id="toastMessage">Berhasil disalin!</span>
            </div>
        </div>
    </div>

    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.footer')
    @include('front-end.layouts.components.bottom-bar')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shareData = {
                title: "{{ $activity->activity_title }}",
                text: "{{ strip_tags(Str::limit($activity->content, 160)) }}",
                url: "{{ url()->current() }}"
            };

            // Modal Elements
            const shareModal = document.getElementById('shareModal');
            const floatingShareBtn = document.getElementById('floatingShareBtn');
            const closeShareModalBtn = document.getElementById('closeShareModal');
            const shareModalBackdrop = document.getElementById('shareModalBackdrop');

            // Open/Close Modal
            function openShareModal() {
                shareModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeShareModal() {
                shareModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Event Listeners
            if (floatingShareBtn) floatingShareBtn.addEventListener('click', openShareModal);
            if (closeShareModalBtn) closeShareModalBtn.addEventListener('click', closeShareModal);
            if (shareModalBackdrop) shareModalBackdrop.addEventListener('click', closeShareModal);

            // Share Functions
            function shareToFacebook() {
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareData.url)}`,
                    '_blank');
            }

            function shareToTwitter() {
                window.open(
                    `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareData.text)}&url=${encodeURIComponent(shareData.url)}`,
                    '_blank');
            }

            function shareToWhatsApp() {
                window.open(
                    `https://api.whatsapp.com/send?text=${encodeURIComponent(shareData.title + ' ' + shareData.url)}`,
                    '_blank');
            }

            function shareToLinkedIn() {
                window.open(
                    `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareData.url)}`,
                    '_blank');
            }

            function shareToTelegram() {
                window.open(
                    `https://t.me/share/url?url=${encodeURIComponent(shareData.url)}&text=${encodeURIComponent(shareData.title)}`,
                    '_blank');
            }

            function shareToEmail() {
                window.location.href =
                    `mailto:?subject=${encodeURIComponent(shareData.title)}&body=${encodeURIComponent(shareData.text + '\n\n' + shareData.url)}`;
            }

            // Copy Function
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Link berhasil disalin!');
                });
            }

            // Toast Function
            function showToast(message) {
                const toast = document.getElementById('toast');
                const toastMessage = document.getElementById('toastMessage');
                toastMessage.textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }

            // Event Delegation for Share Buttons
            document.addEventListener('click', function(e) {
                const platformBtn = e.target.closest('[data-platform]');
                if (platformBtn) {
                    const platform = platformBtn.dataset.platform;
                    switch (platform) {
                        case 'facebook':
                            shareToFacebook();
                            break;
                        case 'twitter':
                            shareToTwitter();
                            break;
                        case 'whatsapp':
                            shareToWhatsApp();
                            break;
                        case 'linkedin':
                            shareToLinkedIn();
                            break;
                        case 'telegram':
                            shareToTelegram();
                            break;
                        case 'email':
                            shareToEmail();
                            break;
                    }
                    closeShareModal();
                }
            });

            // Copy Link Buttons
            document.getElementById('copyLinkBtn')?.addEventListener('click', () => copyToClipboard(shareData.url));
            document.getElementById('copyLinkBtnMain')?.addEventListener('click', () => copyToClipboard(shareData
                .url));
            document.getElementById('copyUrlBtn')?.addEventListener('click', () => copyToClipboard(shareData.url));
        });
    </script>
</body>

</html>
