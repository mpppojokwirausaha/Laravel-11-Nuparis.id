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

    <!-- Meta Tags for Activities Page -->
    <meta name="title" content="Aktivitas & Kegiatan - NUPARIS.ID" />
    <meta name="description"
        content="Ikuti berbagai aktivitas, workshop, seminar, dan kegiatan menarik seputar perizinan bisnis, pengembangan usaha, dan networking bersama komunitas." />
    <meta name="keywords"
        content="aktivitas bisnis, workshop perizinan, seminar UMKM, kegiatan usaha, networking bisnis, komunitas wirausaha" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Aktivitas & Kegiatan - NUPARIS.ID" />
    <meta property="og:description"
        content="Ikuti berbagai aktivitas, workshop, seminar, dan kegiatan menarik seputar perizinan bisnis dan pengembangan usaha." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Aktivitas NUPARIS" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="NUPARIS.ID" />
    <meta property="article:section" content="Aktivitas & Kegiatan" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="Aktivitas & Kegiatan - NUPARIS.ID" />
    <meta property="twitter:description"
        content="Ikuti berbagai aktivitas, workshop, seminar, dan kegiatan menarik seputar perizinan bisnis, pengembangan usaha, dan networking bersama komunitas." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Aktivitas NUPARIS" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="Aktivitas & Kegiatan - NUPARIS.ID" />
    <meta property="linkedin:description"
        content="Kumpulan aktivitas, workshop, seminar, dan kegiatan menarik seputar perizinan bisnis dan pengembangan usaha." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Aktivitas NUPARIS" />

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
                            <span>Aktivitas</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-3">Aktivitas & Kegiatan</h1>
                        <p class="text-red-100 max-w-2xl">Ikuti berbagai aktivitas, workshop, seminar, dan kegiatan
                            menarik seputar perizinan bisnis, pengembangan usaha, dan networking bersama komunitas.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4 lg:mt-0">
                        <div class="text-center">
                            <div id="totalActivitiesCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Total Aktivitas</div>
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
                                    placeholder="Cari aktivitas atau workshop...">
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
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Lokasi</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua Lokasi</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="online" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Online</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="location" value="offline" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Offline</span>
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

            <!-- Activities Section -->
            <section>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-primary pl-3">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Semua Aktivitas</h2>
                        <span id="activitiesCount"
                            class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">
                            0 Aktivitas
                        </span>
                    </div>
                </div>

                <!-- Activities Container -->
                <div id="activitiesContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Aktivitas akan dimuat via AJAX -->
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary">
                    </div>
                    <p class="mt-4 text-gray-600">Memuat aktivitas...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada aktivitas yang ditemukan</h3>
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

    <!-- Main Custom Script with jQuery -->
    <script>
        $(document).ready(function() {
            // State variables untuk client-side pagination
            const state = {
                currentPage: 1,
                itemsPerPage: 12,
                totalPages: 1,
                filters: {
                    category: [],
                    location: 'all',
                    searchQuery: ''
                },
                allActivities: [],
                filteredActivities: []
            };

            // DOM Elements cache
            const elements = {
                activitiesContainer: $('#activitiesContainer'),
                loadingIndicator: $('#loadingIndicator'),
                noResults: $('#noResults'),
                activitiesCount: $('#activitiesCount'),
                totalActivitiesCount: $('#totalActivitiesCount'),
                searchInput: $('#searchInput'),
                clearSearch: $('#clearSearch'),
                resetSearch: $('#resetSearch'),
                filterButton: $('#filterButton'),
                filterPanel: $('#filterPanel'),
                categoriesContainer: $('#categoriesContainer'),
                applyFilter: $('#applyFilter'),
                cancelFilter: $('#cancelFilter'),
                paginationContainer: $('#paginationContainer')
            };

            // Initialize
            setupEventListeners();
            loadActivitiesFromController();

            async function loadActivitiesFromController() {
                showLoading();

                try {
                    const response = await $.ajax({
                        url: '{{ route('activity-data') }}',
                        method: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.success && response.activitiesData && Array.isArray(response.activitiesData)) {
                        state.allActivities = response.activitiesData;

                        updateActivitiesCounts(state.allActivities);
                        renderCategories();
                        filterAndPaginate();
                    } else {
                        showNoResults();
                    }

                } catch (error) {
                    console.error('Error loading activities:', error);
                    showNoResults();
                }
            }

            function updateActivitiesCounts(activities) {
                const total = activities.length;

                if (elements.totalActivitiesCount.length) {
                    elements.totalActivitiesCount.text(total);
                }
                if (elements.activitiesCount.length) {
                    elements.activitiesCount.text(`${total} Aktivitas`);
                }
            }

            function setupEventListeners() {
                // Search input events
                if (elements.searchInput.length) {
                    let searchTimeout;

                    elements.searchInput.on('input', function() {
                        if ($(this).val().trim() !== '') {
                            elements.clearSearch.removeClass('hidden');
                        } else {
                            elements.clearSearch.addClass('hidden');
                        }

                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            state.filters.searchQuery = $(this).val().trim();
                            state.currentPage = 1;
                            filterAndPaginate();
                        }, 500);
                    });

                    elements.searchInput.on('keypress', function(e) {
                        if (e.key === 'Enter') {
                            state.filters.searchQuery = $(this).val().trim();
                            state.currentPage = 1;
                            filterAndPaginate();
                        }
                    });
                }

                // Clear search
                if (elements.clearSearch.length) {
                    elements.clearSearch.on('click', function() {
                        elements.searchInput.val('');
                        state.filters.searchQuery = '';
                        state.currentPage = 1;
                        $(this).addClass('hidden');
                        filterAndPaginate();
                    });
                }

                // Filter button
                if (elements.filterButton.length) {
                    elements.filterButton.on('click', function() {
                        elements.filterPanel.toggleClass('hidden');
                    });
                }

                // Apply filter
                if (elements.applyFilter.length) {
                    elements.applyFilter.on('click', applyFilters);
                }

                // Cancel filter
                if (elements.cancelFilter.length) {
                    elements.cancelFilter.on('click', function() {
                        elements.filterPanel.addClass('hidden');
                    });
                }

                // Reset search
                if (elements.resetSearch.length) {
                    elements.resetSearch.on('click', function() {
                        clearAllFilters();
                        elements.searchInput.val('');
                        state.filters.searchQuery = '';
                        elements.clearSearch.addClass('hidden');
                        filterAndPaginate();
                    });
                }

                // Close filter panel when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#filterPanel').length &&
                        !$(e.target).closest('#filterButton').length) {
                        elements.filterPanel.addClass('hidden');
                    }
                });
            }

            function renderCategories() {
                if (!elements.categoriesContainer.length) return;

                const categories = new Set();
                $.each(state.allActivities, function(index, activity) {
                    const category = activity.category || 'Umum';
                    if (category) {
                        categories.add(category);
                    }
                });

                elements.categoriesContainer.empty();
                categories.forEach(category => {
                    const label = $('<label>', {
                        class: 'flex items-center cursor-pointer',
                        html: `
                            <input type="checkbox" name="category" value="${category}"
                                class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                            <span class="text-gray-700 text-sm">${category}</span>
                        `
                    });
                    elements.categoriesContainer.append(label);
                });
            }

            function applyFilters() {
                // Get category filters
                const categoryCheckboxes = $('input[name="category"]:checked');
                state.filters.category = categoryCheckboxes.map(function() {
                    return $(this).val();
                }).get();

                // Get location
                const locationRadio = $('input[name="location"]:checked');
                if (locationRadio.length) {
                    state.filters.location = locationRadio.val();
                }

                elements.filterPanel.addClass('hidden');
                state.currentPage = 1;
                filterAndPaginate();
            }

            function filterAndPaginate() {
                showLoading();

                setTimeout(() => {
                    state.filteredActivities = filterActivities();
                    updateFilteredCount(state.filteredActivities.length);
                    state.totalPages = Math.ceil(state.filteredActivities.length / state.itemsPerPage);

                    if (state.currentPage > state.totalPages && state.totalPages > 0) {
                        state.currentPage = state.totalPages;
                    }

                    const paginatedActivities = getCurrentPageActivities(state.filteredActivities);
                    renderActivities(paginatedActivities);
                    renderPagination();
                    hideLoading();
                }, 300);
            }

            function filterActivities() {
                let filtered = [...state.allActivities];

                if (state.filters.searchQuery) {
                    const query = state.filters.searchQuery.toLowerCase();
                    filtered = filtered.filter(activity => {
                        const title = (activity.activity_title || activity.title || '').toLowerCase();
                        const description = (activity.activity_description || activity.description || '')
                            .toLowerCase();
                        const location = (activity.location || '').toLowerCase();
                        const category = (activity.category || '').toLowerCase();

                        return title.includes(query) ||
                            description.includes(query) ||
                            location.includes(query) ||
                            category.includes(query);
                    });
                }

                if (state.filters.category.length > 0) {
                    filtered = filtered.filter(activity => {
                        const category = activity.category || 'Umum';
                        return state.filters.category.includes(category);
                    });
                }

                if (state.filters.location !== 'all') {
                    filtered = filtered.filter(activity => {
                        const location = (activity.location || 'Online').toLowerCase();
                        if (state.filters.location === 'online') {
                            return location.includes('online') || location === 'virtual' || location ===
                                'daring';
                        } else {
                            return !location.includes('online') && location !== 'virtual' && location !==
                                'daring';
                        }
                    });
                }

                // Sort by date (newest first)
                filtered.sort((a, b) => {
                    try {
                        const dateA = new Date(a.created_at || 0);
                        const dateB = new Date(b.created_at || 0);
                        return dateB - dateA;
                    } catch (error) {
                        return 0;
                    }
                });

                return filtered;
            }

            function getCurrentPageActivities(filteredActivities) {
                const startIndex = (state.currentPage - 1) * state.itemsPerPage;
                const endIndex = Math.min(startIndex + state.itemsPerPage, filteredActivities.length);
                return filteredActivities.slice(startIndex, endIndex);
            }

            function updateFilteredCount(count) {
                if (elements.activitiesCount.length) {
                    elements.activitiesCount.text(`${count} Aktivitas`);
                }
            }

            function renderActivities(activities) {
                if (activities.length === 0) {
                    showNoResults();
                    return;
                }

                hideNoResults();

                if (elements.activitiesContainer.length) {
                    elements.activitiesContainer.empty();
                }

                $.each(activities, function(index, activity) {
                    const activityCard = createActivityCard(activity);
                    if (elements.activitiesContainer.length) {
                        elements.activitiesContainer.append(activityCard);
                    }
                });
            }

            function renderPagination() {
                if (!elements.paginationContainer.length) {
                    return;
                }

                if (state.totalPages <= 1 || state.filteredActivities.length <= state.itemsPerPage) {
                    elements.paginationContainer.empty();
                    return;
                }

                elements.paginationContainer.empty();

                const paginationWrapper = $('<div>', {
                    class: 'flex flex-col md:flex-row justify-center items-center gap-4'
                });

                const paginationNav = $('<nav>', {
                    class: 'flex items-center space-x-2'
                });

                const prevButton = $('<button>', {
                    class: 'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
                    html: '<i class="fas fa-chevron-left"></i>',
                    disabled: state.currentPage === 1
                }).on('click', function() {
                    if (state.currentPage > 1) {
                        state.currentPage--;
                        filterAndPaginate();
                    }
                });

                paginationNav.append(prevButton);

                const maxVisiblePages = 5;
                let startPage = Math.max(1, state.currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(state.totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                if (startPage > 1) {
                    const firstPageButton = createPageButton(1);
                    paginationNav.append(firstPageButton);

                    if (startPage > 2) {
                        const ellipsis = $('<span>', {
                            class: 'px-2 text-gray-400',
                            text: '...'
                        });
                        paginationNav.append(ellipsis);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = createPageButton(i);
                    paginationNav.append(pageButton);
                }

                if (endPage < state.totalPages) {
                    if (endPage < state.totalPages - 1) {
                        const ellipsis = $('<span>', {
                            class: 'px-2 text-gray-400',
                            text: '...'
                        });
                        paginationNav.append(ellipsis);
                    }

                    const lastPageButton = createPageButton(state.totalPages);
                    paginationNav.append(lastPageButton);
                }

                const nextButton = $('<button>', {
                    class: 'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
                    html: '<i class="fas fa-chevron-right"></i>',
                    disabled: state.currentPage === state.totalPages
                }).on('click', function() {
                    if (state.currentPage < state.totalPages) {
                        state.currentPage++;
                        filterAndPaginate();
                    }
                });
                paginationNav.append(nextButton);

                const infoDiv = $('<div>', {
                    class: 'text-sm text-gray-500'
                });
                const start = (state.currentPage - 1) * state.itemsPerPage + 1;
                const end = Math.min(state.currentPage * state.itemsPerPage, state.filteredActivities.length);
                infoDiv.html(
                    `Menampilkan <span class="font-semibold">${start}-${end}</span> dari <span class="font-semibold">${state.filteredActivities.length}</span> aktivitas`
                );

                paginationWrapper.append(paginationNav);
                paginationWrapper.append(infoDiv);
                elements.paginationContainer.append(paginationWrapper);
            }

            function createPageButton(pageNumber) {
                const isActive = state.currentPage === pageNumber;
                const button = $('<button>', {
                    class: `px-3 py-2 rounded-lg transition-colors ${isActive ? 'page-active bg-primary text-white border-primary' : 'border border-gray-300 text-gray-600 lg:hover:bg-gray-50'}`,
                    text: pageNumber
                }).on('click', function() {
                    if (state.currentPage !== pageNumber) {
                        state.currentPage = pageNumber;
                        filterAndPaginate();
                    }
                });
                return button;
            }

            function createActivityCard(activity) {
                const title = activity.activity_title || activity.title || 'Judul Aktivitas';
                const description = activity.activity_description || activity.description || '';
                const image = activity.activity_image || activity.image || '';
                const category = activity.category || 'Umum';
                const slug = activity.activity_slug || activity.slug || activity.id || '';
                const location = activity.location || activity.activity_location || 'Online';
                const views = activity.views || 0;
                const created_at = activity.created_at || '';

                let formattedDate = 'Tanggal tidak tersedia';
                try {
                    if (created_at) {
                        const activityDate = new Date(created_at);
                        formattedDate = activityDate.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                } catch (e) {
                    console.error('Error parsing date:', e);
                }

                const stripHtml = (html) => {
                    if (!html) return '';
                    const div = document.createElement('div');
                    div.innerHTML = html;
                    return div.textContent || div.innerText || '';
                };

                const excerpt = stripHtml(description).substring(0, 100) + (stripHtml(description).length > 100 ?
                    '...' : '');

                const defaultImage =
                    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w-400&q-80';
                let imageSrc = defaultImage;

                if (image && image.trim() !== '') {
                    if (image.startsWith('http') || image.startsWith('//')) {
                        imageSrc = image;
                    } else {
                        imageSrc = `{{ asset('storage/') }}/${image}`;
                    }
                }

                const card = $('<a>', {
                    href: `/activity/${slug}`,
                    class: 'block h-full',
                    html: `
        <article class="group bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 flex flex-col h-full animate-fade-in-up lg:hover:-translate-y-1 transition-all duration-300 lg:hover:shadow-xl">
            <!-- Gambar -->
            <div class="relative h-48 overflow-hidden">
                <img src="${imageSrc}"
                    alt="${title}"
                    class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500"
                    onerror="this.src='${defaultImage}'">
                
                <!-- Logo NUPARIS -->
                <div class="absolute top-2 left-2 z-10">
                    <div class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-sm">
                        <img src="{{ asset('storage/' . $infos->meta_image) }}"
                            alt="NUPARIS Logo" class="w-6 h-6">
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="absolute top-2 right-2">
                    <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm">
                        ${formattedDate}
                    </span>
                </div>
            </div>

            <!-- Konten -->
            <div class="p-4 flex flex-col flex-grow">
                <!-- Header: Kategori & Viewer -->
                <div class="flex justify-between items-center mb-3">
                    <!-- Kategori -->
                    <div>
                        <span class="text-xs font-semibold text-primary uppercase tracking-wide">
                            ${category}
                        </span>
                    </div>
                    
                    <!-- Viewer -->
                    <div class="flex items-center space-x-1">
                        <i class="far fa-eye text-xs text-gray-400"></i>
                        <span class="text-xs text-gray-600 font-medium">${views}</span>
                    </div>
                </div>

                <!-- Judul -->
                <h3 class="font-bold text-gray-800 mb-3 text-sm line-clamp-2 lg:group-hover:text-primary transition-colors">
                    ${title}
                </h3>

                <!-- Lokasi -->
                <div class="mb-3 flex items-center">
                    <i class="fas fa-map-marker-alt text-xs text-gray-400 mr-2"></i>
                    <span class="text-xs text-gray-600">${location}</span>
                </div>

                <!-- Deskripsi -->
                <p class="text-gray-500 text-xs line-clamp-2 mb-4 flex-grow">
                    ${excerpt || 'Deskripsi tidak tersedia'}
                </p>
            </div>
        </article>
    `
                });

                return card;
            }

            function showLoading() {
                elements.loadingIndicator.removeClass('hidden');
                elements.noResults.addClass('hidden');
                elements.activitiesContainer.empty();
                elements.paginationContainer.empty();
            }

            function hideLoading() {
                elements.loadingIndicator.addClass('hidden');
            }

            function showNoResults() {
                elements.noResults.removeClass('hidden');
                elements.activitiesContainer.empty();
                elements.paginationContainer.empty();
                elements.loadingIndicator.addClass('hidden');
            }

            function hideNoResults() {
                elements.noResults.addClass('hidden');
            }

            function clearAllFilters() {
                $('input[name="category"]').prop('checked', false);
                $('input[name="location"][value="all"]').prop('checked', true);

                state.filters = {
                    category: [],
                    location: 'all',
                    searchQuery: ''
                };

                elements.filterPanel.addClass('hidden');
                state.currentPage = 1;
                filterAndPaginate();
            }
        });
    </script>
</body>

</html>
