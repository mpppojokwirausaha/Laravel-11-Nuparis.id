<!DOCTYPE html>
<html lang="id">

<head>
    @include('front-end.layouts.components.seo-meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans bg-slate-50 text-gray-800 min-h-screen">

    @include('front-end.layouts.components.header')

    <!-- Main Content -->
    <main class="pt-8 lg:pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-slate-600">
                <ol class="flex flex-wrap items-center space-x-2">
                    <li><a href="{{ route('landingpage') }}" class="hover:text-primary transition-colors">Beranda</a>
                    </li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('article-more') }}" class="hover:text-primary transition-colors">Perizinan
                            & Non Perizinan</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-slate-800 font-medium">{{ $article->article_title }}</li>
                </ol>
            </nav>

            <!-- Article Container -->
            <article class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden mb-8">
                <!-- Article Hero -->
                <div class="relative h-64 sm:h-80 lg:h-96 overflow-hidden">
                    <img src="{{ $article->article_image ? asset('storage/' . $article->article_image) : 'https://picsum.photos/seed/berita-detail/1200/600' }}"
                        alt="{{ $article->article_title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <!-- Logo -->
                    <div class="absolute top-4 left-4 z-20 opacity-80">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <img src="{{ asset('storage/' . $infos->meta_image) }}" alt="NUPARIS Logo" class="w-8 h-8">
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="absolute top-4 right-4 z-20">
                        <span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">Berita
                            Utama</span>
                    </div>

                    <!-- Article Info -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 pt-16 text-white">
                        <div class="flex items-center space-x-4 mb-3 text-sm">
                            <span class="flex items-center space-x-1">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ $article->created_at->translatedFormat('d M Y') }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <i class="far fa-clock"></i>
                                <span>{{ $article->created_at->translatedFormat('H:i') }} WIB</span>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight mb-4">
                            {{ $article->article_title }}</h1>
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

                <!-- Article Meta -->
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-eye"></i>
                                <span class="text-sm font-medium">{{ $article->views ?? 0 }} <span
                                        class="hidden lg:inline">views</span></span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-comment"></i>
                                <span class="text-sm font-medium">{{ $article->comments_count ?? 0 }} <span
                                        class="hidden lg:inline">komentar</span></span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-600">
                                <i class="far fa-share-square"></i>
                                <span class="text-sm font-medium" id="shareCount">{{ $article->shares_count ?? 0 }}
                                    <span class="hidden lg:inline">shares</span></span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button data-platform="linkedin" onclick="shareToLinkedIn()"
                                class="w-10 h-10 rounded-full bg-[#0A66C2] text-white hover:bg-[#004182] transition-colors flex items-center justify-center">
                                <i class="fab fa-linkedin-in"></i>
                            </button>
                            <button data-platform="whatsapp" onclick="shareToWhatsApp()"
                                class="w-10 h-10 rounded-full bg-[#25D366] text-white hover:bg-[#22C35E] transition-colors flex items-center justify-center">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button onclick="copyToClipboard()"
                                class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 hover:bg-slate-300 transition-colors flex items-center justify-center">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Article Content -->
                <div class="p-6 sm:p-8 lg:p-8">
                    <div
                        class="prose prose-lg max-w-none prose-headings:text-slate-800 prose-headings:font-bold prose-p:text-slate-600 prose-p:leading-relaxed prose-ul:list-disc prose-ol:list-decimal prose-a:text-primary hover:prose-a:underline prose-img:rounded-lg prose-img:w-full">
                        {!! $article->cleaned_content !!}
                    </div>
                </div>
            </article>
        </div>
    </main>

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
                title: "{{ $article->article_title }}",
                text: "{{ strip_tags(Str::limit($article->content, 160)) }}",
                url: "{{ url()->current() }}"
            };

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
            function copyToClipboard() {
                navigator.clipboard.writeText(shareData.url).then(() => {
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

            // Make functions globally available
            window.shareToFacebook = shareToFacebook;
            window.shareToTwitter = shareToTwitter;
            window.shareToWhatsApp = shareToWhatsApp;
            window.shareToLinkedIn = shareToLinkedIn;
            window.shareToTelegram = shareToTelegram;
            window.shareToEmail = shareToEmail;
            window.copyToClipboard = copyToClipboard;
        });
    </script>
</body>

</html>
