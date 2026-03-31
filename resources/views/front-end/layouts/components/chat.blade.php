<div class="fixed bottom-[6rem] md:bottom-[7rem] lg:bottom-[2rem] right-6 z-[9999]">
    <!-- Chat Bubble -->
    <button id="chatToggle"
        class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-red-600 hover:bg-red-700 shadow-lg flex items-center justify-center cursor-pointer transition-all duration-200 relative">
        <i class="fas fa-headset text-white text-xl md:text-2xl"></i>
        <div id="notificationBadge"
            class="absolute -top-1 -right-1 w-5 h-5 md:w-6 md:h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center border-2 border-white">
            1
        </div>
    </button>

    <!-- Chat Window -->
    <div id="chatWindow"
        class="hidden fixed w-[calc(100vw-2rem)] h-[75vh] max-h-[75vh] bottom-[10rem] right-4 left-4 z-50 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col
            sm:w-[calc(100vw-4rem)] sm:left-auto sm:right-6 sm:bottom-[8rem]
            md:w-[65%] md:max-h-[70vh] md:bottom-[12rem]
            lg:w-[40%] lg:bottom-[7rem]
            xl:w-[20%] xl:bottom-[7rem]">

        <!-- Chat Header -->
        <div class="bg-red-600 text-white p-4 md:p-5 flex justify-between items-center rounded-t-2xl flex-shrink-0">
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-lg md:text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base md:text-lg font-bold">Bantuan NUPARIS</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="relative">
                            <div class="w-3 h-3 md:w-4 md:h-4 bg-green-500 rounded-full"></div>
                            <div
                                class="absolute inset-0 w-3 h-3 md:w-4 md:h-4 bg-green-400 rounded-full animate-ping opacity-75">
                            </div>
                        </div>
                        <p class="text-xs md:text-sm opacity-90">Online • 24/7 Support</p>
                    </div>
                </div>
            </div>
            <button id="chatClose"
                class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-times text-sm md:text-base"></i>
            </button>
        </div>

        <!-- State Management: 0 = chat, 1 = form, 2 = success -->
        <div id="chatState" data-state="0" class="flex-1 flex flex-col min-h-0">

            <!-- State 0: Chat Interface -->
            <div id="stateChat" class="flex-1 flex flex-col min-h-0">
                <!-- Chat Body -->
                <div class="flex-1 flex flex-col min-h-0">
                    <!-- Chat Messages Area -->
                    <div id="chatBody" class="flex-1 p-4 md:p-5 overflow-y-auto bg-gray-50 space-y-4 min-h-0">
                        <!-- Initial messages -->
                        <div class="flex items-start space-x-3 animate-fade-in">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-robot text-red-600 text-sm md:text-base"></i>
                            </div>
                            <div class="bg-white rounded-xl rounded-tl-none px-4 py-3 max-w-[85%] shadow-sm">
                                <p class="text-gray-800 text-sm md:text-base">Selamat datang di layanan bantuan
                                    NUPARIS! 🎉</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    <script>
                                        var now = new Date();
                                        document.write(now.getHours().toString().padStart(2, '0') + ':' +
                                            now.getMinutes().toString().padStart(2, '0'));
                                    </script>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 animate-fade-in">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-robot text-red-600 text-sm md:text-base"></i>
                            </div>
                            <div class="bg-white rounded-xl rounded-tl-none px-4 py-3 max-w-[85%] shadow-sm">
                                <p class="text-gray-800 text-sm md:text-base">Kami menyediakan berbagai layanan
                                    pendampingan untuk kebutuhan bisnis Anda.</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    <script>
                                        var now = new Date();
                                        document.write(now.getHours().toString().padStart(2, '0') + ':' +
                                            now.getMinutes().toString().padStart(2, '0'));
                                    </script>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 animate-fade-in">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-robot text-red-600 text-sm md:text-base"></i>
                            </div>
                            <div class="bg-white rounded-xl rounded-tl-none px-4 py-3 max-w-[85%] shadow-sm">
                                <p class="text-gray-800 text-sm md:text-base">Halo! 👋 Saya Virtual Assistant
                                    NUPARIS. Ada yang bisa saya bantu hari ini?</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    <script>
                                        var now = new Date();
                                        document.write(now.getHours().toString().padStart(2, '0') + ':' +
                                            now.getMinutes().toString().padStart(2, '0'));
                                    </script>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    <div class="p-4 md:p-5 border-t border-gray-200 bg-white flex-shrink-0">
                        <button id="startTicket"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center transition-all duration-200 shadow-md hover:shadow-lg active:scale-[0.98]">
                            <i class="fas fa-ticket-alt mr-3 text-base md:text-lg"></i>
                            <span class="text-sm md:text-base">Buat Tiket Baru</span>
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-2">Respon dalam 1-5 menit</p>
                    </div>
                </div>
            </div>

            <!-- State 1: Ticket Form -->
            <div id="stateForm" class="hidden flex-1 flex flex-col min-h-0">
                <!-- Form Header -->
                <div class="p-4 md:p-5 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                    <div class="flex items-center">
                        <button id="backToChat"
                            class="flex items-center text-gray-600 hover:text-red-600 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2 text-sm md:text-base"></i>
                            <span class="font-medium text-sm md:text-base">Kembali</span>
                        </button>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="flex-1 p-4 md:p-5 overflow-y-auto space-y-5 md:space-y-6 min-h-0">
                    <!-- Judul Tiket -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-heading mr-2 text-red-600"></i>Judul Tiket <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ticket_title" id="ticket_title"
                            class="w-full px-4 py-3 text-sm md:text-base border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200"
                            placeholder="Masukkan judul tiket Anda">
                    </div>

                    <!-- Bidang Pendampingan -->
                    <div class="relative">
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-tag mr-2 text-red-600"></i>Pilih Bidang Pendampingan <span
                                class="text-red-500">*</span>
                        </label>

                        <!-- Custom Select Trigger -->
                        <button type="button" id="bidangTrigger"
                            class="w-full px-4 py-3 text-sm md:text-base border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200 cursor-pointer bg-white flex items-center justify-between text-left hover:border-gray-400">
                            <span id="bidangDisplay" class="text-gray-500 truncate pr-2">Pilih bidang
                                pendampingan</span>
                            <i
                                class="fas fa-chevron-down text-gray-400 transition-transform duration-200 flex-shrink-0 ml-2"></i>
                        </button>

                        <!-- Hidden Input -->
                        <input type="hidden" name="consultant_specialization_uuid" id="consultant_specialization_uuid"
                            value="">

                        <!-- Custom Dropdown -->
                        <div id="bidangDropdown"
                            class="hidden absolute z-50 top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-64 overflow-hidden">
                            <!-- Search Bar -->
                            <div class="sticky top-0 bg-white border-b border-gray-200 p-2">
                                <div class="relative">
                                    <i
                                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="bidangSearch"
                                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
                                        placeholder="Cari bidang...">
                                </div>
                            </div>

                            <!-- Options Container -->
                            <div id="bidangOptions" class="py-1 overflow-y-auto max-h-48">
                                <!-- Options will be populated here -->
                            </div>

                            <!-- Loading State -->
                            <div id="bidangLoading" class="py-6 text-center">
                                <div
                                    class="inline-block w-6 h-6 border-2 border-red-600 border-t-transparent rounded-full animate-spin">
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Memuat bidang pendampingan...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="bidangEmpty" class="hidden py-6 text-center border-t border-gray-100">
                                <i class="fas fa-search text-gray-300 text-xl mb-2"></i>
                                <p class="text-sm text-gray-500">Tidak ada bidang ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Klien -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-user mr-2 text-red-600"></i>Nama Klien <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" id="ticket_name_client"
                            class="w-full px-4 py-3 text-sm md:text-base border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200"
                            placeholder="Masukkan nama Anda">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-envelope mr-2 text-red-600"></i>Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="ticket_email" id="ticket_email"
                            class="w-full px-4 py-3 text-sm md:text-base border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200"
                            placeholder="contoh@email.com">
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fab fa-whatsapp mr-2 text-red-600"></i>WhatsApp <span
                                class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <div
                                class="flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">
                                <span class="text-gray-600 text-sm md:text-base">+62</span>
                            </div>
                            <input type="tel" name="ticket_whatsapp" id="ticket_whatsapp"
                                class="flex-1 px-4 py-3 text-sm md:text-base border border-gray-300 rounded-r-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200"
                                placeholder="81234567890">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: tanpa kode negara (+62)</p>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-align-left mr-2 text-red-600"></i>Deskripsi Masalah <span
                                class="text-red-500">*</span>
                        </label>
                        <textarea name="ticket_content" id="ticket_content" rows="4"
                            class="w-full px-4 py-3 text-sm md:text-base border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-500 outline-none transition-all duration-200 resize-none"
                            placeholder="Jelaskan secara detail masalah atau kebutuhan pendampingan Anda..."></textarea>
                        <div id="charCounter" class="text-xs text-gray-500 text-right mt-1">0/1000 karakter</div>
                    </div>

                    <!-- Upload File -->
                    <div>
                        <label class="block text-gray-700 mb-2 font-medium text-sm md:text-base">
                            <i class="fas fa-paperclip mr-2 text-red-600"></i>Lampiran File (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 md:p-6 text-center hover:bg-gray-50 transition-colors duration-200 cursor-pointer"
                            id="fileDropArea">
                            <input type="file" name="ticket_document_support" id="ticket_document_support"
                                class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="space-y-2" id="fileUploadContent">
                                <i class="fas fa-cloud-upload-alt text-2xl md:text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium text-red-600">Klik untuk upload</span> atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500">Format: PDF, JPG, JPEG, PNG (Maks. 2MB)</p>
                                <p class="text-xs text-red-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i> Hanya 1 file yang dapat diupload
                                </p>
                            </div>
                            <div id="filePreview" class="hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">File terpilih:</span>
                                    <button type="button" id="clearFiles"
                                        class="text-xs text-red-600 hover:text-red-800 transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>Hapus
                                    </button>
                                </div>
                                <div id="fileList" class="space-y-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-4 md:p-5 border-t border-gray-200 bg-white flex-shrink-0">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button id="cancelForm" type="button"
                            class="flex-1 py-3 px-4 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors duration-200 text-sm md:text-base">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button id="submitForm" type="button"
                            class="flex-1 py-3 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-200 shadow-md active:scale-[0.98] text-sm md:text-base">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Tiket
                        </button>
                    </div>
                </div>
            </div>

            <!-- State 2: Success Message -->
            <div id="stateSuccess" class="hidden flex-1 flex flex-col min-h-0">
                <!-- Success Content -->
                <div class="flex-1 flex flex-col items-center justify-center p-6 min-h-0">
                    <div
                        class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4 md:mb-6 animate-slide-up">
                        <i class="fas fa-check-circle text-2xl md:text-3xl text-green-600"></i>
                    </div>
                    <h4 class="text-lg md:text-xl font-bold text-gray-800 mb-3 text-center">Permintaan Berhasil
                        Dikirim! 🎉</h4>
                    <p class="text-sm text-gray-600 mb-4 md:mb-6 text-center max-w-xs md:max-w-md">
                        Terima kasih telah menghubungi NUPARIS. Konsultan kami akan menghubungi Anda segera.
                    </p>

                    <div
                        class="bg-green-50 rounded-xl p-4 md:p-5 mb-4 md:mb-6 border border-green-200 w-full max-w-xs">
                        <p class="text-xs text-gray-500 mb-2 text-center">Nomor Tiket Anda</p>
                        <p id="ticketNumber"
                            class="text-lg md:text-xl font-bold text-red-600 text-center tracking-wide">
                            Loading...
                        </p>
                        <p class="text-xs text-gray-500 mt-3 text-center">Simpan nomor ini untuk pengecekan status
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 mb-4 md:mb-6 text-center max-w-xs md:max-w-md">
                        Detail tiket telah dikirim ke email anda.<br>
                        <a href="https://mail.google.com/mail/u/0/#inbox"
                            class="text-red-600 font-semibold hover:underline">Cek Email Disini</a>.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="p-4 md:p-5 border-t border-gray-200 bg-white flex-shrink-0 space-y-3">
                    <button id="newTicketBtn"
                        class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-200 shadow-md active:scale-[0.98] text-sm md:text-base">
                        <i class="fas fa-plus mr-2"></i> Buat Tiket Lain
                    </button>

                    <button id="closeSuccess"
                        class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors duration-200 text-sm md:text-base">
                        <i class="fas fa-comments mr-2"></i> Kembali ke Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // State management
        let currentState = 0;
        let uploadedFiles = [];
        let bidangList = [];

        // Get elements
        const chatToggle = document.getElementById('chatToggle');
        const chatWindow = document.getElementById('chatWindow');
        const chatClose = document.getElementById('chatClose');
        const notificationBadge = document.getElementById('notificationBadge');

        // State elements
        const stateChat = document.getElementById('stateChat');
        const stateForm = document.getElementById('stateForm');
        const stateSuccess = document.getElementById('stateSuccess');

        // Chat elements
        const chatBody = document.getElementById('chatBody');
        const startTicket = document.getElementById('startTicket');
        const backToChat = document.getElementById('backToChat');

        // Form elements
        const ticketNameClient = document.getElementById('ticket_name_client');
        const ticketTitle = document.getElementById('ticket_title');
        const ticketEmail = document.getElementById('ticket_email');
        const ticketWhatsapp = document.getElementById('ticket_whatsapp');
        const ticketContent = document.getElementById('ticket_content');
        const charCounter = document.getElementById('charCounter');
        const cancelForm = document.getElementById('cancelForm');
        const submitForm = document.getElementById('submitForm');

        // Custom Select elements
        const bidangTrigger = document.getElementById('bidangTrigger');
        const bidangDisplay = document.getElementById('bidangDisplay');
        const consultantSpecialization = document.getElementById('consultant_specialization_uuid');
        const bidangDropdown = document.getElementById('bidangDropdown');
        const bidangSearch = document.getElementById('bidangSearch');
        const bidangOptions = document.getElementById('bidangOptions');
        const bidangLoading = document.getElementById('bidangLoading');
        const bidangEmpty = document.getElementById('bidangEmpty');

        // File upload elements
        const fileDropArea = document.getElementById('fileDropArea');
        const fileInput = document.getElementById('ticket_document_support');
        const fileUploadContent = document.getElementById('fileUploadContent');
        const filePreview = document.getElementById('filePreview');
        const fileList = document.getElementById('fileList');
        const clearFiles = document.getElementById('clearFiles');

        // Success elements
        const newTicketBtn = document.getElementById('newTicketBtn');
        const closeSuccess = document.getElementById('closeSuccess');
        const ticketNumber = document.getElementById('ticketNumber');

        // File type icons mapping
        const fileIcons = {
            pdf: 'fas fa-file-pdf',
            jpg: 'fas fa-file-image',
            jpeg: 'fas fa-file-image',
            png: 'fas fa-file-image',
            default: 'fas fa-file'
        };

        // File type colors
        const fileColors = {
            pdf: 'bg-red-100 text-red-600',
            jpg: 'bg-yellow-100 text-yellow-600',
            jpeg: 'bg-yellow-100 text-yellow-600',
            png: 'bg-yellow-100 text-yellow-600',
            default: 'bg-gray-100 text-gray-600'
        };

        // ==================== CUSTOM SELECT FUNCTIONS ====================

        // Toggle dropdown
        bidangTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            bidangDropdown.classList.contains('hidden') ? openBidangDropdown() : closeBidangDropdown();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!bidangTrigger.contains(e.target) && !bidangDropdown.contains(e.target)) {
                closeBidangDropdown();
            }
        });

        // Search functionality
        bidangSearch.addEventListener('input', function() {
            filterBidangOptions(this.value.toLowerCase());
        });

        // Prevent click from closing dropdown
        bidangSearch.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Open dropdown
        function openBidangDropdown() {
            bidangDropdown.classList.remove('hidden');
            bidangTrigger.querySelector('i').style.transform = 'rotate(180deg)';
            bidangTrigger.classList.add('border-red-500', 'ring-2', 'ring-red-500');
            // Focus search
            setTimeout(() => {
                bidangSearch.focus();
                bidangSearch.select();
            }, 50);
        }
        // Close dropdown
        function closeBidangDropdown() {
            bidangDropdown.classList.add('hidden');
            const icon = bidangTrigger.querySelector('i');
            if (icon) icon.style.transform = 'rotate(0deg)';
            bidangTrigger.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
            // Clear search
            bidangSearch.value = '';
            filterBidangOptions('');
        }

        function filterBidangOptions(searchTerm = '') {
            if (!bidangList || bidangList.length === 0) return;
            const filtered = bidangList.filter(b => b.name.toLowerCase().includes(searchTerm));
            renderBidangOptions(filtered);
            // Show/hide empty state
            if (filtered.length === 0) {
                bidangEmpty.classList.remove('hidden');
                bidangOptions.classList.add('hidden');
            } else {
                bidangEmpty.classList.add('hidden');
                bidangOptions.classList.remove('hidden');
            }
        }

        // Render options
        function renderBidangOptions(bidangData) {
            bidangOptions.innerHTML = '';

            bidangData.forEach(bidang => {
                const div = document.createElement('div');
                div.className =
                    'px-4 py-2.5 hover:bg-red-50 cursor-pointer transition-colors duration-150 flex items-center group';
                div.innerHTML = `
                <div class="w-5 h-5 rounded border border-gray-300 mr-3 flex items-center justify-center flex-shrink-0 group-hover:border-red-400">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-600 ${consultantSpecialization.value === bidang.uuid ? '' : 'hidden'}"></div>
                </div>
                <span class="text-sm text-gray-700 truncate">${bidang.name}</span>
            `;
                if (consultantSpecialization.value === bidang.uuid) {
                    div.classList.add('bg-red-50', 'text-red-700');
                }
                div.addEventListener('click', function() {
                    selectBidangOption(bidang);
                });
                bidangOptions.appendChild(div);
            });
        }

        function selectBidangOption(bidang) {
            consultantSpecialization.value = bidang.uuid;
            bidangDisplay.textContent = bidang.name;
            bidangDisplay.classList.remove('text-gray-500');
            bidangDisplay.classList.add('text-gray-800', 'font-medium');
            bidangTrigger.classList.remove('border-red-500');
            closeBidangDropdown();
        }

        // Select option
        function showFallbackBidang() {
            bidangLoading.classList.add('hidden');
            bidangOptions.classList.remove('hidden');
            bidangOptions.innerHTML =
                '<p class="text-sm text-gray-500 text-center py-4">Gagal memuat data. Silakan coba lagi.</p>';
        }

        function loadBidangPendampingan() {
            bidangDisplay.textContent = 'Pilih bidang pendampingan';
            bidangDisplay.classList.remove('text-gray-800', 'font-medium');
            bidangDisplay.classList.add('text-gray-500');
            consultantSpecialization.value = '';

            // Show loading
            bidangLoading.classList.remove('hidden');
            bidangOptions.classList.add('hidden');
            bidangEmpty.classList.add('hidden');

            $.ajax({
                url: '{{ route('ticket.bidang') }}',
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response && response.bidang && Array.isArray(response.bidang)) {
                        // Process data
                        bidangList = response.bidang.map(b => ({
                            uuid: b.uuid,
                            name: b.consultant_specialization_name
                        }));

                        // Hide loading
                        bidangLoading.classList.add('hidden');
                        bidangOptions.classList.remove('hidden');

                        // Render options
                        renderBidangOptions(bidangList);
                    } else {
                        showFallbackBidang();
                    }
                },
                error: function() {
                    showFallbackBidang();
                }
            });
        }


        // ==================== MAIN FUNCTIONS ====================

        // Toggle chat window
        chatToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            chatWindow.classList.toggle('hidden');
            notificationBadge.classList.add('hidden');

            if (!chatWindow.classList.contains('hidden')) {
                setTimeout(scrollChatToBottom, 100);
            }
        });

        // Close chat
        chatClose.addEventListener('click', function() {
            chatWindow.classList.add('hidden');
            setTimeout(() => changeState(0), 100);
        });

        document.addEventListener('click', function(e) {
            if (currentState !== 0) return;

            if (!chatWindow.contains(e.target) && !chatToggle.contains(e.target)) {
                chatWindow.classList.add('hidden');
                setTimeout(() => changeState(0), 100);
            }
        });

        // Start ticket
        startTicket.addEventListener('click', function() {
            addMessage('user', 'Saya ingin membuat tiket pendampingan.');
            setTimeout(() => {
                addMessage('support', 'Silakan isi form permintaan pendampingan di bawah ini.');
                changeState(1);
            }, 500);
        });
        // Back to chat
        backToChat.addEventListener('click', function() {
            changeState(0);
        });

        // Cancel form
        cancelForm.addEventListener('click', function() {
            addMessage('support', 'Form dibatalkan. Ada yang bisa saya bantu?');
            changeState(0);
            resetForm();
        });

        // Submit form
        submitForm.addEventListener('click', function() {
            if (validateForm()) {
                submitFormData();
            }
        });

        // New ticket from success
        newTicketBtn.addEventListener('click', function() {
            addMessage('user', 'Saya ingin membuat tiket pendampingan baru.');
            setTimeout(() => {
                addMessage('support', 'Silakan isi form permintaan pendampingan di bawah ini.');
                changeState(1);
            }, 500);
        });

        // Close from success
        closeSuccess.addEventListener('click', function() {
            changeState(0);
        });

        // Character counter
        ticketContent.addEventListener('input', function() {
            const length = ticketContent.value.length;
            if (length > 1000) {
                ticketContent.value = ticketContent.value.substring(0, 1000);
            }
            const shown = Math.min(length, 1000);
            charCounter.textContent = shown + '/1000 karakter';
            charCounter.className = shown > 900 ? 'text-xs text-red-600 text-right mt-1' :
                shown > 800 ? 'text-xs text-yellow-600 text-right mt-1' :
                'text-xs text-gray-500 text-right mt-1';
        });

        // WhatsApp formatting
        ticketWhatsapp.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // File upload functionality (SINGLE FILE)
        fileDropArea.addEventListener('click', function() {
            fileInput.click();
        });

        // Handle file input change (SINGLE FILE)
        fileDropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileDropArea.classList.add('border-red-400', 'bg-red-50');
        });

        fileDropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileDropArea.classList.remove('border-red-400', 'bg-red-50');
        });

        fileDropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileDropArea.classList.remove('border-red-400', 'bg-red-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) handleFile(files[0]);
        });

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) handleFile(e.target.files[0]);
        });

        // Clear file
        clearFiles.addEventListener('click', function() {
            clearSelectedFile();
        });

        // ==================== HELPER FUNCTIONS ====================

        function changeState(newState) {
            currentState = newState;

            // Hide all states
            stateChat.classList.add('hidden');
            stateForm.classList.add('hidden');
            stateSuccess.classList.add('hidden');

            // Show current state
            switch (newState) {
                case 0: // Chat
                    stateChat.classList.remove('hidden');
                    setTimeout(scrollChatToBottom, 50);
                    break;
                case 1: // Form
                    stateForm.classList.remove('hidden');
                    // Load bidang pendampingan saat form dibuka
                    loadBidangPendampingan();
                    setTimeout(() => ticketTitle.focus(), 100);
                    break;
                case 2: // Success
                    stateSuccess.classList.remove('hidden');
                    break;
            }
        }

        function addMessage(type, text) {
            const div = document.createElement('div');
            div.className =
                `flex items-start space-x-3 mb-4 animate-fade-in ${type === 'user' ? 'justify-end' : ''}`;

            const time = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            if (type === 'user') {
                div.innerHTML = `
                <div class="flex flex-col items-end max-w-[80%]">
                    <div class="bg-red-600 text-white rounded-xl rounded-tr-none px-4 py-3 shadow-sm">
                        <p class="text-sm">${text}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${time}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>`;
            } else {
                div.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-red-600 text-sm"></i>
                </div>
                <div class="flex flex-col max-w-[80%]">
                    <div class="bg-white rounded-xl rounded-tl-none px-4 py-3 shadow-sm">
                        <p class="text-gray-800 text-sm">${text}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${time}</p>
                </div>`;
            }
            chatBody.appendChild(div);
            scrollChatToBottom();
        }

        function scrollChatToBottom() {
            setTimeout(() => {
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 50);
        }

        function handleFile(file) {
            // Check file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('File Terlalu Besar', file.name + ' melebihi batas 2MB.');
                return;
            }

            // Check file type
            const allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/jpg',
                'image/png'
            ];

            if (!allowedTypes.includes(file.type)) {
                showAlert('Format Tidak Didukung',
                    file.name +
                    ' memiliki format yang tidak didukung. Hanya PDF, JPG, JPEG, PNG yang diperbolehkan.'
                );
                return;
            }

            // Clear previous files and add new one
            uploadedFiles = [{
                file,
                id: Date.now(),
                name: file.name,
                size: formatFileSize(file.size),
                type: getFileExtension(file.name),
                mimeType: file.type
            }];

            updateFilePreview();
        }

        function getFileExtension(filename) {
            return filename.split('.').pop().toLowerCase();
        }

        function updateFilePreview() {
            if (uploadedFiles.length === 0) {
                filePreview.classList.add('hidden');
                fileUploadContent.classList.remove('hidden');
                return;
            }
            fileUploadContent.classList.add('hidden');
            filePreview.classList.remove('hidden');
            fileList.innerHTML = '';

            uploadedFiles.forEach(fileData => {
                const el = document.createElement('div');
                el.className =
                    'flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200';
                const iconClass = fileIcons[fileData.type] || fileIcons.default;
                const colorClass = fileColors[fileData.type] || fileColors.default;
                el.innerHTML = `
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center ${colorClass}">
                        <i class="${iconClass} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${fileData.name}</p>
                        <p class="text-xs text-gray-500">${fileData.size}</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 ml-2 p-1 rounded-full hover:bg-red-50 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>`;
                el.querySelector('button').addEventListener('click', clearSelectedFile);
                fileList.appendChild(el);
            });
        }

        function clearSelectedFile() {
            uploadedFiles = [];
            updateFilePreview();
            fileInput.value = '';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // FIX: semua karakter â€¢ diganti • yang benar
        function validateForm() {
            let isValid = true;
            let errors = [];

            // Validasi Judul Tiket
            if (!ticketNameClient.value.trim()) {
                isValid = false;
                errors.push('• Nama klien harus diisi');
                ticketNameClient.classList.add('border-red-500');
            } else {
                ticketNameClient.classList.remove('border-red-500');
            }

            // Validasi Bidang Pendampingan
            if (!ticketTitle.value.trim()) {
                isValid = false;
                errors.push('• Judul tiket harus diisi');
                ticketTitle.classList.add('border-red-500');
            } else {
                ticketTitle.classList.remove('border-red-500');
            }

            // Validasi Bidang Pendampingan
            if (!consultantSpecialization.value) {
                isValid = false;
                errors.push('• Bidang pendampingan harus dipilih');
                bidangTrigger.classList.add('border-red-500');
            } else {
                bidangTrigger.classList.remove('border-red-500');
            }

            // Validasi Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!ticketEmail.value.trim()) {
                isValid = false;
                errors.push('• Email harus diisi');
                ticketEmail.classList.add('border-red-500');
            } else if (!emailRegex.test(ticketEmail.value)) {
                isValid = false;
                errors.push('• Format email tidak valid');
                ticketEmail.classList.add('border-red-500');
            } else {
                ticketEmail.classList.remove('border-red-500');
            }

            // Validasi WhatsApp
            if (!ticketWhatsapp.value.trim()) {
                isValid = false;
                errors.push('• Nomor WhatsApp harus diisi');
                ticketWhatsapp.classList.add('border-red-500');
            } else if (ticketWhatsapp.value.length < 10) {
                isValid = false;
                errors.push('• Nomor WhatsApp minimal 10 digit');
                ticketWhatsapp.classList.add('border-red-500');
            } else {
                ticketWhatsapp.classList.remove('border-red-500');
            }
            // Validasi Deskripsi
            if (!ticketContent.value.trim()) {
                isValid = false;
                errors.push('• Deskripsi masalah harus diisi');
                ticketContent.classList.add('border-red-500');
            } else if (ticketContent.value.trim().length < 20) {
                isValid = false;
                errors.push('• Deskripsi masalah minimal 20 karakter');
                ticketContent.classList.add('border-red-500');
            } else {
                ticketContent.classList.remove('border-red-500');
            }

            // File validation
            uploadedFiles.forEach(fileData => {
                if (fileData.file.size > 2 * 1024 * 1024) {
                    isValid = false;
                    errors.push('• ' + fileData.name + ' melebihi batas 2MB');
                }
            });

            if (!isValid) {
                showAlert('Perhatian', 'Harap perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
            }
            return isValid;
        }

        function submitFormData() {
            // Create FormData
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('ticket_name_client', ticketNameClient.value);
            formData.append('ticket_title', ticketTitle.value);
            formData.append('consultant_specialization_uuid', consultantSpecialization.value);
            formData.append('ticket_email', ticketEmail.value);
            formData.append('ticket_whatsapp', '62' + ticketWhatsapp.value);
            formData.append('ticket_content', ticketContent.value);

            // Add file if exists
            if (uploadedFiles.length > 0) {
                formData.append('ticket_document_support', uploadedFiles[0].file);
            }

            // Show loading
            submitForm.disabled = true;
            const originalHTML = submitForm.innerHTML;
            submitForm.innerHTML =
                '<span class="inline-flex items-center justify-center gap-2"><span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Mengirim...</span>';

            // AJAX call
            $.ajax({
                url: '{{ route('ticket.post') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        let fileInfo = uploadedFiles.length > 0 ? ' dengan file lampiran' : '';
                        addMessage('user', 'Tiket pendampingan "' + ticketTitle.value +
                            '" telah dikirim' + fileInfo + '.');

                        // FIX: handle berbagai kemungkinan struktur response
                        if (response.ticket && response.ticket.code) {
                            ticketNumber.textContent = response.ticket.code;
                        } else if (response.data && response.data.ticket_code) {
                            ticketNumber.textContent = response.data.ticket_code;
                        } else {
                            ticketNumber.textContent = 'Tiket-' + Date.now();
                        }

                        resetForm();
                        changeState(2);
                    } else {
                        showAlert('Error', response.message ||
                            'Terjadi kesalahan saat mengirim tiket.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengirim tiket.';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert('Error', errorMessage);
                },
                complete: function() {
                    submitForm.disabled = false;
                    submitForm.innerHTML = originalHTML;
                }
            });
        }

        function resetForm() {
            ticketNameClient.value = '';
            ticketTitle.value = '';
            consultantSpecialization.value = '';
            ticketEmail.value = '';
            ticketWhatsapp.value = '';
            ticketContent.value = '';
            charCounter.textContent = '0/1000 karakter';
            charCounter.className = 'text-xs text-gray-500 text-right mt-1';

            // Reset custom select
            bidangDisplay.textContent = 'Pilih bidang pendampingan';
            bidangDisplay.classList.remove('text-gray-800', 'font-medium');
            bidangDisplay.classList.add('text-gray-500');
            bidangTrigger.classList.remove('border-red-500');

            // Clear files
            clearSelectedFile();

            // Remove error styling
            [ticketNameClient, ticketTitle, ticketEmail, ticketWhatsapp, ticketContent].forEach(el => el
                .classList.remove('border-red-500'));
        }

        function showAlert(title, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className =
                'fixed top-4 right-4 z-[9999] px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg shadow-lg max-w-xs';

            alertDiv.innerHTML = `
            <div class="flex items-start space-x-2">
                <i class="fas fa-exclamation-triangle text-lg mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-medium text-sm mb-1">${title}</p>
                    <p class="text-xs whitespace-pre-line">${message}</p>
                </div>
                <button class="text-yellow-600 hover:text-yellow-800 ml-2 p-1" onclick="this.closest('.fixed').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentElement) alertDiv.remove();
            }, 5000);
        }

        // Remove error styling
        setTimeout(() => {
            notificationBadge.classList.add('hidden');
        }, 5000);
        // Initial state
        changeState(0);
    });
</script>
