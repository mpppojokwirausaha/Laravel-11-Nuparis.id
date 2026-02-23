<nav id="bottom-navbar"
    class="bg-white fixed bottom-0 left-0 right-0 h-20 z-50 bg-white/98 backdrop-blur-[15px] border-t border-slate-200 shadow-[0_-4px_20px_rgba(0,0,0,0.1)] transform-gpu lg:hidden">
    <div class="flex justify-between items-center h-full px-4 py-2">
        <!-- Beranda -->
        <a href="{{ route('landingpage') }}"
            class="flex flex-col items-center justify-center py-2 px-3 rounded-lg {{ request()->routeIs('landingpage') ? 'bg-red-50 text-primary' : 'text-slate-600 hover:text-primary' }}">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs font-medium">Beranda</span>
        </a>

        <!-- Berita -->
        <a href="{{ route('news-more') }}"
            class="flex flex-col items-center justify-center py-2 px-3 rounded-lg {{ request()->routeIs(['news-more', 'news-detail']) ? 'bg-red-50 text-primary' : 'text-slate-600 hover:text-primary' }}">
            <i class="fas fa-newspaper text-xl mb-1"></i>
            <span class="text-xs font-medium">Berita</span>
        </a>

        <!-- Perizinan & Non Perizinan -->
        <a href="{{ route('article-more') }}"
            class="flex flex-col items-center justify-center py-2 px-3 rounded-lg {{ request()->routeIs(['article-more', 'article-detail']) ? 'bg-red-50 text-primary' : 'text-slate-600 hover:text-primary' }}">
            <i class="fas fa-file-contract text-xl mb-1"></i>
            <span class="text-xs font-medium">Perizinan</span>
        </a>

        <!-- Event -->
        <a href="{{ route('event-more') }}"
            class="flex flex-col items-center justify-center py-2 px-3 rounded-lg {{ request()->routeIs(['event-more', 'event-detail']) ? 'bg-red-50 text-primary' : 'text-slate-600 hover:text-primary' }}">
            <i class="fas fa-calendar-alt text-xl mb-1"></i>
            <span class="text-xs font-medium">Event</span>
        </a>

        <!-- Properti -->
        <a href="{{ route('property-more') }}"
            class="hidden md:flex lg:hidden flex-col items-center justify-center py-2 px-3 rounded-lg {{ request()->routeIs(['property-more', 'property-detail']) ? 'bg-red-50 text-primary' : 'text-slate-600 hover:text-primary' }}">
            <i class="fas fa-building text-xl mb-1"></i>
            <span class="text-xs font-medium">Properti</span>
        </a>

        <!-- Menu -->
        <button id="bottom-menu-btn"
            class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-slate-600 hover:text-primary">
            <i class="fas fa-bars text-xl mb-1"></i>
            <span class="text-xs font-medium">Menu</span>
        </button>
    </div>
</nav>

<!-- Bottom Menu Modal (UBAH MENJADI 3 KOLOM GRID) -->
<div id="bottom-menu-modal" class="fixed inset-0 bg-black/50 hidden z-[10001]">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Menu</h3>
            <button id="close-bottom-menu" class="text-slate-400 hover:text-slate-800">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Menu Grid 3 Kolom -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <!-- Beranda -->
            <a href="{{ route('landingpage') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs('landingpage') ? 'bg-red-50 text-primary border border-red-100' : 'bg-red-50 text-slate-600 hover:bg-red-100 hover:text-primary' }} transition-all">
                <i class="fas fa-home text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Beranda</span>
            </a>

            <!-- Berita -->
            <a href="{{ route('news-more') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs(['news-more', 'news-detail']) ? 'bg-blue-50 text-primary border border-blue-100' : 'bg-blue-50 text-slate-600 hover:bg-blue-100 hover:text-blue-600' }} transition-all">
                <i class="fas fa-newspaper text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Berita</span>
            </a>

            <!-- Perizinan -->
            <a href="{{ route('article-more') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs(['article-more', 'article-detail']) ? 'bg-emerald-50 text-primary border border-emerald-100' : 'bg-emerald-50 text-slate-600 hover:bg-emerald-100 hover:text-emerald-600' }} transition-all">
                <i class="fas fa-file-contract text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Perizinan</span>
            </a>

            <!-- Aktivitas -->
            <a href="{{ route('activity-more') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs(['activity-more', 'activity-detail']) ? 'bg-purple-50 text-primary border border-purple-100' : 'bg-purple-50 text-slate-600 hover:bg-purple-100 hover:text-purple-600' }} transition-all">
                <i class="fas fa-running text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Aktivitas</span>
            </a>

            <!-- Event -->
            <a href="{{ route('event-more') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs(['event-more', 'event-detail']) ? 'bg-amber-50 text-primary border border-amber-100' : 'bg-amber-50 text-slate-600 hover:bg-amber-100 hover:text-amber-600' }} transition-all">
                <i class="fas fa-calendar-alt text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Event</span>
            </a>

            <!-- Properti -->
            <a href="{{ route('property-more') }}"
                class="flex flex-col items-center justify-center p-3 rounded-xl {{ request()->routeIs(['property-more', 'property-detail']) ? 'bg-cyan-50 text-primary border border-cyan-100' : 'bg-cyan-50 text-slate-600 hover:bg-cyan-100 hover:text-cyan-600' }} transition-all">
                <i class="fas fa-building text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Properti</span>
            </a>

            {{-- <!-- Profil -->
            <a href="#"
                class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 transition-all">
                <i class="fas fa-user text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Profil</span>
            </a>

            <!-- Pengaturan -->
            <a href="#"
                class="flex flex-col items-center justify-center p-3 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100 transition-all">
                <i class="fas fa-cog text-lg mb-2"></i>
                <span class="text-xs font-medium text-center">Setting</span>
            </a> --}}
        </div>

        <!-- Action Buttons -->
        @if (Auth::check())
            <a href="{{ route('filament.management.pages.dashboard') }}"
                class="flex items-center justify-center bg-primary text-white px-5 py-2 rounded-full font-medium hover:bg-primary/90 transition-all duration-200 shadow-lg">
                Dashboard
            </a>
        @else
            <div class="pt-4 border-t border-slate-200">
                <a href="https://nuparis.id/management/login"
                    class="inline-flex justify-center items-center w-full bg-primary text-white py-3 rounded-xl font-medium hover:bg-red-700 transition mb-3">
                    Masuk
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    // Bottom Menu Functionality
    const bottomMenuBtn = document.getElementById('bottom-menu-btn');
    const bottomMenuModal = document.getElementById('bottom-menu-modal');
    const closeBottomMenu = document.getElementById('close-bottom-menu');
    const mobileWrapper = document.getElementById('mobile-content-wrapper');

    if (bottomMenuBtn) {
        bottomMenuBtn.addEventListener('click', () => {
            bottomMenuModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (mobileWrapper) mobileWrapper.style.overflow = 'hidden';
        });
    }

    if (closeBottomMenu) {
        closeBottomMenu.addEventListener('click', () => {
            bottomMenuModal.classList.add('hidden');
            document.body.style.overflow = '';
            if (mobileWrapper) mobileWrapper.style.overflow = 'auto';
        });
    }

    if (bottomMenuModal) {
        bottomMenuModal.addEventListener('click', (e) => {
            if (e.target === bottomMenuModal) {
                bottomMenuModal.classList.add('hidden');
                document.body.style.overflow = '';
                if (mobileWrapper) mobileWrapper.style.overflow = 'auto';
            }
        });
    }

    // Tablet responsive adjustment
    function adjustForTablet() {
        const navbar = document.getElementById('bottom-navbar');
        const wrapper = document.getElementById('mobile-content-wrapper');

        if (window.matchMedia('(min-width: 768px) and (max-width: 1023px)').matches) {
            // Tablet
            if (navbar) {
                navbar.classList.remove('h-20');
                navbar.classList.add('h-24');
            }
            if (wrapper) {
                wrapper.classList.remove('pb-20');
                wrapper.classList.add('pb-24');
            }
        } else {
            // Mobile
            if (navbar) {
                navbar.classList.remove('h-24');
                navbar.classList.add('h-20');
            }
            if (wrapper) {
                wrapper.classList.remove('pb-24');
                wrapper.classList.add('pb-20');
            }
        }
    }

    // Initial adjustment
    adjustForTablet();

    // Adjust on resize
    window.addEventListener('resize', adjustForTablet);
</script>
