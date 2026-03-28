<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Tags for Articles Page -->
    <meta name="title" content="Perizinan & Non Perizinan & Panduan Perizinan Bisnis - NUPARIS.ID" />
    <meta name="description"
        content="Temukan Perizinan & Non Perizinan informatif, panduan lengkap, dan tips praktis seputar perizinan bisnis, regulasi terbaru, dan informasi penting untuk pengembangan usaha Anda." />
    <meta name="keywords"
        content="perizinan bisnis, izin usaha, regulasi, panduan UMKM, Perizinan & Non Perizinan bisnis, legalitas usaha" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Perizinan & Non Perizinan & Panduan Perizinan Bisnis - NUPARIS.ID" />
    <meta property="og:description"
        content="Kumpulan Perizinan & Non Perizinan informatif dan panduan lengkap seputar perizinan bisnis, regulasi terbaru, dan tips praktis untuk pengembangan usaha Anda." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Perizinan & Non Perizinan Perizinan NUPARIS" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="NUPARIS.ID" />
    <meta property="article:section" content="Perizinan & Bisnis" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="Perizinan & Non Perizinan & Panduan Perizinan Bisnis - NUPARIS.ID" />
    <meta property="twitter:description"
        content="Temukan Perizinan & Non Perizinan informatif, panduan lengkap, dan tips praktis seputar perizinan bisnis, regulasi terbaru, dan informasi penting untuk pengembangan usaha Anda." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Perizinan & Non Perizinan Perizinan NUPARIS" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="Perizinan & Non Perizinan & Panduan Perizinan Bisnis - NUPARIS.ID" />
    <meta property="linkedin:description"
        content="Kumpulan Perizinan & Non Perizinan informatif dan panduan lengkap seputar perizinan bisnis, regulasi terbaru, dan tips praktis untuk pengembangan usaha Anda." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Perizinan & Non Perizinan Perizinan NUPARIS" />

    <!-- Tailwind Configuration -->
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans text-gray-800 bg-gray-50 h-full overflow-x-hidden">

    @include('front-end.layouts.components.header')

    <!-- Content Wrapper -->
    <div class="h-full overflow-y-auto pt-0 lg:pt-0">
        <!-- Header Hero Section -->
        <section class="bg-gradient-to-r from-red-600 to-red-700 text-white pt-6 pb-12 lg:pt-24 lg:pb-16">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex-1">
                        <nav class="flex items-center text-sm text-red-100 mb-2">
                            <a href="{{ route('landingpage') }}"
                                class="lg:hover:text-white transition-colors">Beranda</a>
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span>Perizinan & Non Perizinan</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-3">Perizinan & Non Perizinan</h1>
                        <p class="text-red-100 max-w-2xl">Temukan Perizinan & Non Perizinan informatif dan panduan
                            lengkap seputar perizinan bisnis, regulasi terbaru, dan informasi penting untuk pengembangan
                            usaha Anda.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4 lg:mt-0">
                        <div class="text-center">
                            <div id="totalArticlesCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Total Perizinan & Non Perizinan</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Search and Filter Section -->
            <section class="mb-10">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Search Bar -->
                    <div class="flex items-center justify-between p-4">
                        <div class="flex-1 flex items-center">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput"
                                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm"
                                    placeholder="Cari Perizinan & Non Perizinan atau informasi perizinan...">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button id="clearSearch"
                                        class="text-gray-400 lg:hover:text-gray-600 hidden transition-colors">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter and Sort Buttons -->
                            <div class="flex ml-3 gap-2">
                                <button id="filterButton"
                                    class="flex items-center justify-center gap-2 bg-gray-100 lg:hover:bg-gray-200 text-gray-700 font-medium px-3 py-2.5 rounded-lg transition-colors text-sm">
                                    <i class="fas fa-filter"></i>
                                    <span class="hidden sm:inline">Filter</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel (Hidden by default) -->
                <div id="filterPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Kategori</h4>
                            <div id="categoriesContainer" class="space-y-2">
                                <!-- Kategori akan dimuat via JavaScript -->
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Rentang Waktu</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua Waktu</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="week" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Minggu Ini</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="month" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Bulan Ini</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="year" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Tahun Ini</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button id="cancelFilter"
                            class="px-4 py-2 text-gray-600 lg:hover:text-gray-800 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button id="applyFilter"
                            class="px-4 py-2 bg-primary lg:hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </section>

            <!-- Articles Section -->
            <section>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-primary pl-3">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Semua Perizinan & Non Perizinan</h2>
                        <span id="articlesCount"
                            class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">
                            0 Perizinan & Non Perizinan
                        </span>
                    </div>
                </div>

                <!-- Articles Container -->
                <div id="articlesContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Perizinan & Non Perizinan akan dimuat via AJAX -->
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary">
                    </div>
                    <p class="mt-4 text-gray-600">Memuat Perizinan & Non Perizinan...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada Perizinan & Non Perizinan yang ditemukan
                    </h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Coba ubah kata kunci pencarian atau filter yang Anda
                        gunakan.</p>
                    <button id="resetSearch"
                        class="bg-primary lg:hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                    </button>
                </div>

                <!-- PAGINATION SECTION -->
                <div id="paginationContainer" class="mt-12"></div>
            </section>
        </main>

        <!-- Footer -->
        @include('front-end.layouts.components.footer')
    </div>
    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.bottom-bar')

    <!-- Main Custom Script dengan jQuery -->
    <script>
        $(document).ready(function() {
            // console.log('DOM loaded with jQuery, initializing article page...');

            // State variables untuk client-side pagination
            let currentPage = 1;
            const itemsPerPage = 12;
            let totalPages = 1;
            let currentFilters = {
                category: [],
                timeRange: 'all',
                searchQuery: ''
            };
            let allArticles = [];
            let filteredArticles = [];

            // Initialize
            setupEventListeners();
            loadArticlesFromController();

            async function loadArticlesFromController() {
                // console.log('Loading articles from controller...');
                showLoading();

                try {
                    const response = await fetch('{{ route('article-data') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    // console.log('Response dari controller:', result);

                    if (result.success && result.articlesData && Array.isArray(result.articlesData)) {
                        allArticles = result.articlesData;
                        // console.log(`Loaded ${allArticles.length} articles from controller`);

                        // Log struktur Perizinan & Non Perizinan pertama untuk debugging
                        if (allArticles.length > 0) {
                            // console.log('Contoh struktur Perizinan & Non Perizinan:', allArticles[0]);
                            // console.log('Perizinan & Non Perizinan kategori:', allArticles[0].article_category);
                            // console.log('Nama kategori:', allArticles[0].article_category?.article_category_name);
                        }

                        updateArticlesCounts(allArticles);
                        renderCategories();
                        filterAndPaginate();
                    } else {
                        console.error('Data tidak valid atau kosong:', result);
                        showNoResults();
                    }
                } catch (error) {
                    console.error('Error loading articles:', error);
                    showNoResults();
                }
            }

            // Fungsi untuk memperbaiki URL gambar
            function fixImageUrl(imagePath) {
                if (!imagePath) {
                    return 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                }

                // Jika sudah URL lengkap
                if (imagePath.startsWith('http') || imagePath.startsWith('//')) {
                    return imagePath;
                }

                // Jika relative path dengan storage/
                if (imagePath.startsWith('storage/')) {
                    return `{{ url('/') }}/${imagePath}`;
                }

                // Jika hanya nama file
                return `{{ url('storage') }}/${imagePath}`;
            }

            // Fungsi untuk mendapatkan kategori dari Perizinan & Non Perizinan
            function getArticleCategory(article) {
                // PRIORITAS UTAMA: Cek dari article_category (berdasarkan struktur response)
                if (article.article_category && typeof article.article_category === 'object') {
                    if (article.article_category.article_category_name) {
                        return article.article_category.article_category_name;
                    } else if (article.article_category.category_name) {
                        return article.article_category.category_name;
                    } else if (article.article_category.name) {
                        return article.article_category.name;
                    }
                }

                // Cek dari badge
                if (article.badge) {
                    if (typeof article.badge === 'object' && article.badge.label) {
                        return article.badge.label;
                    } else if (typeof article.badge === 'string') {
                        return article.badge;
                    }
                }

                // Cek dari article.article.badge
                if (article.article && article.article.badge) {
                    if (typeof article.article.badge === 'object' && article.article.badge.label) {
                        return article.article.badge.label;
                    } else {
                        return article.article.badge;
                    }
                }

                // Cek dari relasi category
                if (article.category) {
                    if (typeof article.category === 'object') {
                        return article.category.category_name || article.category.name || 'Perizinan';
                    } else {
                        return article.category;
                    }
                }

                // Cek dari article_category_name langsung
                if (article.article_category_name) {
                    return article.article_category_name;
                }

                // Default
                return 'Perizinan';
            }

            // Fungsi escape HTML untuk keamanan
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function updateArticlesCounts(articles) {
                const total = articles.length;
                // console.log(`Total articles: ${total}`);

                $('#totalArticlesCount').text(total);
                $('#articlesCount').text(`${total} Perizinan & Non Perizinan`);
            }

            function setupEventListeners() {
                // console.log('Setting up event listeners with jQuery...');

                // Search functionality dengan debounce
                let searchTimeout;
                $('#searchInput').on('input', function() {
                    const value = $(this).val().trim();

                    if (value !== '') {
                        $('#clearSearch').removeClass('hidden');
                    } else {
                        $('#clearSearch').addClass('hidden');
                    }

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentFilters.searchQuery = value;
                        currentPage = 1;
                        // console.log('Search triggered:', currentFilters.searchQuery);
                        filterAndPaginate();
                    }, 500);
                });

                $('#searchInput').on('keypress', function(e) {
                    if (e.key === 'Enter') {
                        currentFilters.searchQuery = $(this).val().trim();
                        currentPage = 1;
                        // console.log('Search (Enter):', currentFilters.searchQuery);
                        filterAndPaginate();
                    }
                });

                $('#clearSearch').on('click', function() {
                    $('#searchInput').val('');
                    currentFilters.searchQuery = '';
                    currentPage = 1;
                    $(this).addClass('hidden');
                    // console.log('Clear search clicked');
                    filterAndPaginate();
                });

                // Filter panel toggle
                $('#filterButton').on('click', function() {
                    $('#filterPanel').toggleClass('hidden');
                    // console.log('Filter panel toggled');
                });

                // Apply filter button
                $('#applyFilter').on('click', applyFilters);

                // Cancel filter button
                $('#cancelFilter').on('click', function() {
                    $('#filterPanel').addClass('hidden');
                    // console.log('Filter panel closed');
                });

                // Reset search button
                $('#resetSearch').on('click', function() {
                    clearAllFilters();
                    $('#searchInput').val('');
                    currentFilters.searchQuery = '';
                    $('#clearSearch').addClass('hidden');
                    // console.log('Reset all filters');
                    filterAndPaginate();
                });

                // Close panel when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#filterPanel, #filterButton').length) {
                        $('#filterPanel').addClass('hidden');
                    }
                });
            }

            function renderCategories() {
                const $categoriesContainer = $('#categoriesContainer');
                if (!$categoriesContainer.length) return;

                // Ekstrak kategori unik dari articles
                const categories = new Set();
                allArticles.forEach(article => {
                    // PRIORITAS: Akses article_category.article_category_name
                    if (article.article_category && article.article_category.article_category_name) {
                        categories.add(article.article_category.article_category_name);
                    }
                    // Fallback ke method getArticleCategory
                    else {
                        const category = getArticleCategory(article);
                        if (category) {
                            categories.add(category);
                        }
                    }
                });

                // console.log(`Found ${categories.size} unique categories:`, Array.from(categories));

                // Render checkbox untuk setiap kategori
                $categoriesContainer.empty();

                if (categories.size === 0) {
                    // Jika tidak ada kategori, tampilkan kategori default
                    const label = $('<label>').addClass('flex items-center cursor-pointer');
                    label.html(`
                    <input type="checkbox" name="category" value="Perizinan"
                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="text-gray-700 text-sm">Perizinan</span>
                `);
                    $categoriesContainer.append(label);
                } else {
                    Array.from(categories).sort().forEach(category => {
                        const label = $('<label>').addClass('flex items-center cursor-pointer');
                        label.html(`
                        <input type="checkbox" name="category" value="${escapeHtml(category)}"
                            class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="text-gray-700 text-sm">${escapeHtml(category)}</span>
                    `);
                        $categoriesContainer.append(label);
                    });
                }
            }

            function applyFilters() {
                // console.log('Applying filters...');

                // Get category filters
                currentFilters.category = $('input[name="category"]:checked').map(function() {
                    return $(this).val();
                }).get();
                // console.log('Selected categories:', currentFilters.category);

                // Get time range
                const timeRangeRadio = $('input[name="timeRange"]:checked');
                if (timeRangeRadio.length) {
                    currentFilters.timeRange = timeRangeRadio.val();
                    // console.log('Time range:', currentFilters.timeRange);
                }

                // Close filter panel
                $('#filterPanel').addClass('hidden');

                // Reset to page 1
                currentPage = 1;

                // Apply filters and paginate
                filterAndPaginate();
            }

            function filterAndPaginate() {
                // console.log('Filtering and paginating...');
                showLoading();

                setTimeout(() => {
                    // 1. Filter Perizinan & Non Perizinan
                    filteredArticles = filterArticles();
                    // console.log(`Filtered articles: ${filteredArticles.length} of ${allArticles.length}`);

                    // 2. Update count
                    updateFilteredCount(filteredArticles.length);

                    // 3. Hitung total pages
                    totalPages = Math.ceil(filteredArticles.length / itemsPerPage);
                    // console.log(`Total pages: ${totalPages}`);

                    // 4. Validasi current page
                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                        // console.log(`Adjusted current page to: ${currentPage}`);
                    }

                    // 5. Dapatkan Perizinan & Non Perizinan untuk halaman saat ini
                    const paginatedArticles = getCurrentPageArticles(filteredArticles);
                    // console.log(`Current page articles: ${paginatedArticles.length}`);

                    // 6. Render Perizinan & Non Perizinan
                    renderArticles(paginatedArticles);

                    // 7. Render pagination
                    renderPagination();

                    // 8. Hide loading
                    hideLoading();

                    // console.log('Filter and paginate completed');
                }, 300);
            }

            function filterArticles() {
                let filtered = [...allArticles];

                // Normalisasi data untuk memudahkan filtering
                filtered = filtered.map(article => {
                    return {
                        ...article,
                        _normalizedCategory: getArticleCategory(article)
                    };
                });

                // Apply search filter
                if (currentFilters.searchQuery) {
                    const query = currentFilters.searchQuery.toLowerCase();
                    filtered = filtered.filter(article => {
                        const title = (article.article_title || '').toLowerCase();
                        const content = (article.article_description || '').toLowerCase();
                        const category = (article._normalizedCategory || '').toLowerCase();

                        return title.includes(query) ||
                            content.includes(query) ||
                            category.includes(query);
                    });
                    // console.log(`After search: ${filtered.length} articles`);
                }

                // Apply category filter
                if (currentFilters.category.length > 0) {
                    filtered = filtered.filter(article => {
                        return currentFilters.category.includes(article._normalizedCategory);
                    });
                    // console.log(`After category filter: ${filtered.length} articles`);
                }

                // Apply time filter
                if (currentFilters.timeRange !== 'all') {
                    const now = new Date();
                    filtered = filtered.filter(article => {
                        const dateString = article.created_at;
                        if (!dateString) return true;

                        const articleDate = new Date(dateString);
                        const diffTime = now - articleDate;
                        const diffDays = diffTime / (1000 * 60 * 60 * 24);

                        switch (currentFilters.timeRange) {
                            case 'week':
                                return diffDays <= 7;
                            case 'month':
                                return diffDays <= 30;
                            case 'year':
                                return diffDays <= 365;
                            default:
                                return true;
                        }
                    });
                    // console.log(`After time filter: ${filtered.length} articles`);
                }

                // Sort by date (newest first)
                filtered.sort((a, b) => {
                    const dateA = new Date(a.created_at || 0);
                    const dateB = new Date(b.created_at || 0);
                    return dateB - dateA;
                });

                return filtered;
            }

            function getCurrentPageArticles(filteredArticles) {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, filteredArticles.length);
                return filteredArticles.slice(startIndex, endIndex);
            }

            function updateFilteredCount(count) {
                // console.log(`Filtered count: ${count}`);
                $('#articlesCount').text(`${count} Perizinan & Non Perizinan`);
            }

            function renderArticles(articles) {
                // console.log(`Rendering ${articles.length} articles...`);

                if (articles.length === 0) {
                    showNoResults();
                    return;
                }

                hideNoResults();

                const $container = $('#articlesContainer');
                $container.empty();

                articles.forEach((article) => {
                    const articleCard = createArticleCard(article);
                    $container.append(articleCard);
                });

                // console.log('Articles rendered');
            }

            function createArticleCard(article) {
                const title = article.article_title || 'Judul tidak tersedia';
                const content = article.article_description || '';
                const image = article.article_image || '';
                const slug = article.article_slug || '#';
                const date = article.created_at || null;

                // Dapatkan kategori menggunakan fungsi yang sudah dibuat
                const category = getArticleCategory(article);

                let formattedDate = 'Tanggal tidak tersedia';
                try {
                    if (date) {
                        const articleDate = new Date(date);
                        if (!isNaN(articleDate.getTime())) {
                            formattedDate = articleDate.toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    }
                } catch (e) {
                    console.error('Error parsing date:', e);
                }

                const stripHtml = (html) => {
                    if (!html) return '';
                    const div = $('<div>').html(html);
                    return div.text() || '';
                };

                const excerpt = stripHtml(content).substring(0, 150) + (stripHtml(content).length > 150 ? '...' :
                    '');

                const defaultImage =
                    'https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                const imageSrc = fixImageUrl(image);

                const card = $('<a>')
                    .attr('href', `/article/${slug}`)
                    .addClass('block h-full');

                card.html(`
                <article class="group bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 flex flex-col h-full animate-fade-in-up lg:hover:-translate-y-1 transition-all duration-300 lg:hover:shadow-xl">
                    <div class="relative h-48 overflow-hidden">
                        <img src="${imageSrc}"
                            alt="${escapeHtml(title)}"
                            class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500"
                            onerror="this.src='${defaultImage}'">
                        
                        <div class="absolute top-2 left-2 z-10 opacity-50">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <img src="${fixImageUrl('{{ $infos->meta_image }}')}"
                                    alt="NUPARIS Logo" class="w-6 h-6" onerror="this.style.display='none'">
                            </div>
                        </div>

                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-bold text-slate-700">
                            <i class="far fa-calendar-alt mr-1"></i>
                            ${formattedDate}
                        </div>
                    </div>

                    <div class="p-4 flex flex-col flex-grow">
                        <span class="text-xs font-bold text-primary uppercase tracking-wider">
                            ${escapeHtml(category)}
                        </span>

                        <h3 class="font-bold text-slate-800 mt-2 mb-2 text-sm line-clamp-2 lg:group-hover:text-primary transition">
                            ${escapeHtml(title)}
                        </h3>

                        <p class="text-slate-500 text-xs line-clamp-3">
                            ${escapeHtml(excerpt) || 'Deskripsi tidak tersedia'}
                        </p>

                        <div class="mt-auto pt-4"></div>
                    </div>
                </article>
            `);

                return card;
            }

            function renderPagination() {
                // console.log(`Rendering pagination for ${totalPages} pages, current page: ${currentPage}`);

                const $container = $('#paginationContainer');
                if (!$container.length) {
                    console.error('Pagination container not found!');
                    return;
                }

                if (totalPages <= 1 || filteredArticles.length <= itemsPerPage) {
                    $container.empty();
                    // console.log('No pagination needed (only 1 page)');
                    return;
                }

                // Clear container
                $container.empty();

                // Create pagination wrapper
                const paginationWrapper = $('<div>').addClass(
                    'flex flex-col md:flex-row justify-center items-center gap-4');

                // Create pagination nav
                const paginationNav = $('<nav>').addClass('flex items-center space-x-2');

                // Previous button
                const prevButton = $('<button>')
                    .addClass(
                        'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    )
                    .html('<i class="fas fa-chevron-left"></i>')
                    .prop('disabled', currentPage === 1)
                    .on('click', function() {
                        if (currentPage > 1) {
                            currentPage--;
                            // console.log(`Previous page: ${currentPage}`);
                            filterAndPaginate();
                        }
                    });
                paginationNav.append(prevButton);

                // Page numbers - maksimal 5 halaman yang ditampilkan
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                // console.log(`Page range: ${startPage} to ${endPage}`);

                // First page button if needed
                if (startPage > 1) {
                    const firstPageButton = createPageButton(1);
                    paginationNav.append(firstPageButton);

                    if (startPage > 2) {
                        const ellipsis = $('<span>').addClass('px-2 text-gray-400').text('...');
                        paginationNav.append(ellipsis);
                    }
                }

                // Page buttons
                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = createPageButton(i);
                    paginationNav.append(pageButton);
                }

                // Last page button if needed
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsis = $('<span>').addClass('px-2 text-gray-400').text('...');
                        paginationNav.append(ellipsis);
                    }

                    const lastPageButton = createPageButton(totalPages);
                    paginationNav.append(lastPageButton);
                }

                // Next button
                const nextButton = $('<button>')
                    .addClass(
                        'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    )
                    .html('<i class="fas fa-chevron-right"></i>')
                    .prop('disabled', currentPage === totalPages)
                    .on('click', function() {
                        if (currentPage < totalPages) {
                            currentPage++;
                            // console.log(`Next page: ${currentPage}`);
                            filterAndPaginate();
                        }
                    });
                paginationNav.append(nextButton);

                // Info text
                const infoDiv = $('<div>').addClass('text-sm text-gray-500');
                const start = (currentPage - 1) * itemsPerPage + 1;
                const end = Math.min(currentPage * itemsPerPage, filteredArticles.length);
                infoDiv.html(
                    `Menampilkan <span class="font-semibold">${start}-${end}</span> dari <span class="font-semibold">${filteredArticles.length}</span> Perizinan & Non Perizinan`
                );

                // Add to wrapper
                paginationWrapper.append(paginationNav);
                paginationWrapper.append(infoDiv);

                // Add wrapper to container
                $container.append(paginationWrapper);

                // console.log('Pagination rendered successfully');
            }

            function createPageButton(pageNumber) {
                const button = $('<button>')
                    .addClass(
                        `px-3 py-2 rounded-lg transition-colors ${currentPage === pageNumber ? 'bg-primary text-white border-primary' : 'border border-gray-300 text-gray-600 lg:hover:bg-gray-50'}`
                    )
                    .text(pageNumber)
                    .on('click', function() {
                        if (currentPage !== pageNumber) {
                            currentPage = pageNumber;
                            // console.log(`Page ${pageNumber} clicked`);
                            filterAndPaginate();
                        }
                    });
                return button;
            }

            function showLoading() {
                // console.log('Showing loading indicator');
                $('#loadingIndicator').removeClass('hidden');
                $('#noResults').addClass('hidden');
                $('#articlesContainer').empty();
                $('#paginationContainer').empty();
            }

            function hideLoading() {
                // console.log('Hiding loading indicator');
                $('#loadingIndicator').addClass('hidden');
            }

            function showNoResults() {
                // console.log('Showing no results message');
                $('#noResults').removeClass('hidden');
                $('#articlesContainer').empty();
                $('#paginationContainer').empty();
                $('#loadingIndicator').addClass('hidden');
            }

            function hideNoResults() {
                $('#noResults').addClass('hidden');
            }

            function clearAllFilters() {
                // console.log('Clearing all filters');

                $('input[name="category"]').prop('checked', false);
                $('input[name="timeRange"][value="all"]').prop('checked', true);

                currentFilters = {
                    category: [],
                    timeRange: 'all',
                    searchQuery: ''
                };

                $('#filterPanel').addClass('hidden');
                currentPage = 1;
            }
        });
    </script>
</body>

</html>
