    <x-filament-panels::page>
        {{-- Form Section --}}
        <div class="mb-6">
            {{ $this->form }}
        </div>

        {{-- Debug Panel - Filament 3 Style --}}


        {{-- Preview Section --}}
        @if ($this->canPreview)
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    📄 PDF Preview dengan QR Code
                </x-slot>

                <x-slot name="description">
                    Drag QR code untuk mengatur posisi, lalu klik "Simpan Posisi" untuk menyimpan koordinat.
                </x-slot>

                {{-- URL Info untuk debugging --}}
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-950 rounded text-xs space-y-1">
                    <div>
                        <strong>PDF:</strong>
                        <a href="{{ $this->pdfUrl }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $this->pdfUrl }}
                        </a>
                    </div>
                    <div>
                        <strong>QR:</strong>
                        <a href="{{ $this->qrUrl }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $this->qrUrl }}
                        </a>
                    </div>
                </div>

                {{-- PDF Container - FIXED: Better structure and positioning --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded border overflow-hidden">
                    <div id="pdf-container" class="relative inline-block border border-gray-300 bg-white"
                        style="position: relative; min-height: 200px;">
                        <canvas id="pdf-canvas" class="block border border-black"></canvas>
                        <img id="qr-overlay" src="{{ $this->qrUrl }}" alt="QR Code"
                            class="absolute cursor-move border-2 border-dashed border-blue-500 bg-white p-1"
                            style="width: 100px; height: 100px; top: 50px; left: 50px; z-index: 10; user-select: none; pointer-events: auto;">
                    </div>
                </div>
            </x-filament::section>

            {{-- Controls --}}
            <div class="mt-4 flex flex-wrap gap-3">
                <button id="save-position-btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    💾 Simpan Posisi
                </button>

                <button id="reset-position-btn" type="button"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🔄 Reset Posisi
                </button>

                <button id="refresh-debug-btn" type="button" wire:click="refreshDebug"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🐛 Refresh Debug
                </button>
            </div>

            {{-- Position Info --}}
            <div id="position-info" class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                Position: <span id="current-position">X: 0, Y: 0</span>
            </div>
        @else
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    ⚠️ Preview Tidak Tersedia
                </x-slot>

                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400 mb-4">
                        @if (!$this->selectedFile)
                            <p>Silakan pilih file PDF dari dropdown di atas.</p>
                        @elseif(!$this->hasPdf)
                            <p>PDF URL tidak tersedia. Coba generate preview lagi.</p>
                        @elseif(!$this->hasQr)
                            <p>QR Code belum di-generate. Coba generate preview lagi.</p>
                        @else
                            <p>Terjadi masalah tidak diketahui. Periksa debug log.</p>
                        @endif
                    </div>

                    @if ($this->selectedFile)
                        <button wire:click="generatePreview" type="button"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            🔄 Coba Generate Lagi
                        </button>
                    @endif
                </div>
            </x-filament::section>
        @endif

        {{-- Scripts --}}
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
            <script>
                // PDF.js Worker
                pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
            </script>
            <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

            <script>
                // Global helper function to dispatch Livewire events
                function dispatchLivewireEvent(eventName, eventData) {
                    console.log(`📡 Dispatching Livewire event: ${eventName}`, eventData);

                    // Try multiple methods to dispatch the event
                    let dispatched = false;

                    // Method 1: Try window.Livewire (Livewire v2/v3)
                    if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                        try {
                            window.Livewire.dispatch(eventName, eventData);
                            console.log('✅ Event dispatched via window.Livewire.dispatch');
                            dispatched = true;
                        } catch (e) {
                            console.warn('⚠️ Failed to dispatch via window.Livewire.dispatch:', e);
                        }
                    }

                    // Method 2: Try Livewire.dispatch (global)
                    if (!dispatched && typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                        try {
                            Livewire.dispatch(eventName, eventData);
                            console.log('✅ Event dispatched via Livewire.dispatch');
                            dispatched = true;
                        } catch (e) {
                            console.warn('⚠️ Failed to dispatch via Livewire.dispatch:', e);
                        }
                    }

                    // Method 3: Try $wire (if available)
                    if (!dispatched && typeof $wire !== 'undefined' && typeof $wire.dispatch === 'function') {
                        try {
                            $wire.dispatch(eventName, eventData);
                            console.log('✅ Event dispatched via $wire.dispatch');
                            dispatched = true;
                        } catch (e) {
                            console.warn('⚠️ Failed to dispatch via $wire.dispatch:', e);
                        }
                    }

                    // Method 4: Try Alpine.js $dispatch (if available)
                    if (!dispatched) {
                        try {
                            const event = new CustomEvent(eventName, {
                                detail: eventData,
                                bubbles: true
                            });
                            document.dispatchEvent(event);
                            console.log('✅ Event dispatched via CustomEvent');
                            dispatched = true;
                        } catch (e) {
                            console.warn('⚠️ Failed to dispatch via CustomEvent:', e);
                        }
                    }

                    // Method 5: Try Livewire hook (alternative method)
                    if (!dispatched) {
                        try {
                            // Create a hidden button and trigger wire:click programmatically
                            const hiddenBtn = document.createElement('button');
                            hiddenBtn.setAttribute('wire:click',
                                `savePosition(${eventData.x}, ${eventData.y}, ${eventData.scale})`);
                            hiddenBtn.style.display = 'none';
                            document.body.appendChild(hiddenBtn);
                            hiddenBtn.click();
                            document.body.removeChild(hiddenBtn);
                            console.log('✅ Event dispatched via hidden button method');
                            dispatched = true;
                        } catch (e) {
                            console.warn('⚠️ Failed to dispatch via hidden button method:', e);
                        }
                    }

                    if (!dispatched) {
                        console.error('❌ All dispatch methods failed. Event not sent:', eventName, eventData);
                    }

                    return dispatched;
                }

                document.addEventListener('alpine:init', () => {
                    Alpine.data('pdfPreview', () => ({
                        currentPdfUrl: null,
                        pdfDocument: null,
                        qrDragInstance: null,
                        isLoading: false,
                        debounceTimer: null,

                        init() {
                            console.log('🚀 PDF Preview Alpine Component Initialized');
                            this.setupApp();
                        },

                        setupApp() {
                            console.log('🎯 Setting up app');

                            // Setup event listeners
                            this.setupEventListeners();

                            // Initial setup
                            this.$nextTick(() => {
                                this.setupPdfPreview();
                            });
                        },

                        setupEventListeners() {
                            // Listen for Livewire events
                            window.addEventListener('preview-updated', (event) => {
                                console.log('📡 Preview updated event:', event.detail);

                                // Clear existing debounce timer
                                if (this.debounceTimer) {
                                    clearTimeout(this.debounceTimer);
                                }

                                // Debounce to avoid premature clearing
                                this.debounceTimer = setTimeout(() => {
                                    this.$nextTick(() => {
                                        // Get PDF URL from event data
                                        const eventData = event.detail[0];
                                        if (eventData && eventData.pdfUrl) {
                                            // Only clear if we're loading a different PDF
                                            if (this.currentPdfUrl !== eventData
                                                .pdfUrl) {
                                                console.log(
                                                    '🔄 Loading different PDF, clearing previous'
                                                );
                                                this
                                                    .clearCanvasQuietly(); // Clear without notification
                                            }
                                            this.setupPdfPreview(eventData.pdfUrl);
                                        }
                                        // Don't auto-clear if no PDF URL - let user decide
                                    });
                                }, 300);
                            });

                            // Only trigger when user explicitly clears preview
                            window.addEventListener('preview-cleared', () => {
                                console.log('🧹 Preview cleared by user');
                                if (this.debounceTimer) {
                                    clearTimeout(this.debounceTimer);
                                }
                                this.clearCanvas(); // This will show the "preview dibersihkan" message
                            });

                            window.addEventListener('position-saved', (event) => {
                                console.log('💾 Position saved event:', event.detail);
                                this.updatePositionDisplay();
                            });
                        },

                        setupPdfPreview(eventPdfUrl = null) {
                            console.log('🎯 Setting up PDF preview');

                            // Get PDF URL from event or backend
                            const pdfUrl = eventPdfUrl || @json($this->pdfUrl ?? '');
                            const canvas = document.getElementById('pdf-canvas');

                            console.log('PDF URL:', pdfUrl);

                            if (!pdfUrl || pdfUrl === 'null' || pdfUrl === '') {
                                console.warn('⚠️ PDF URL is empty or invalid');
                                // Don't clear, just return silently
                                return;
                            }

                            if (!canvas) {
                                console.warn('⚠️ Canvas element not found');
                                return;
                            }

                            // Skip if same PDF is already loaded
                            if (this.currentPdfUrl === pdfUrl && this.pdfDocument) {
                                console.log('📄 PDF already loaded, setting up QR only');
                                this.setupQrDragging();
                                return;
                            }

                            console.log('📄 Loading new PDF:', pdfUrl);
                            this.isLoading = true;
                            this.currentPdfUrl = pdfUrl;

                            const loadingTask = pdfjsLib.getDocument({
                                url: pdfUrl,
                                cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                                cMapPacked: true,
                                verbosity: 0
                            });

                            loadingTask.promise
                                .then(pdf => {
                                    console.log('✅ PDF loaded successfully');
                                    this.pdfDocument = pdf;
                                    return pdf.getPage(1);
                                })
                                .then(page => {
                                    const scale = 1.5;
                                    const viewport = page.getViewport({
                                        scale
                                    });
                                    const context = canvas.getContext('2d');

                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    console.log(
                                        `🎨 Rendering PDF to canvas (${canvas.width}x${canvas.height})`);

                                    const renderContext = {
                                        canvasContext: context,
                                        viewport: viewport
                                    };

                                    return page.render(renderContext).promise;
                                })
                                .then(() => {
                                    console.log('✅ PDF rendered successfully');
                                    this.isLoading = false;
                                    this.setupQrDragging();
                                })
                                .catch(error => {
                                    console.error('❌ Error loading/rendering PDF:', error);
                                    this.isLoading = false;
                                    // Only clear on critical errors that user should know about
                                    if (error.name !== 'AbortException' && error.name !==
                                        'InvalidPDFException') {
                                        console.error('Critical PDF error, clearing canvas silently');
                                        this.clearCanvasQuietly();
                                    }
                                });
                        },

                        setupQrDragging() {
                            const qr = document.getElementById('qr-overlay');
                            const container = document.getElementById('pdf-container');
                            const canvas = document.getElementById('pdf-canvas');

                            if (!qr || !container || !canvas) {
                                console.warn('⚠️ Required elements not found for QR dragging');
                                return;
                            }

                            console.log('🎯 Setting up QR dragging with constraints');

                            // Clean up existing interact instance
                            if (this.qrDragInstance) {
                                this.qrDragInstance.unset();
                            }

                            // Reset QR position to center of canvas
                            this.resetQrPosition();

                            // Setup new interact instance with proper canvas boundaries
                            this.qrDragInstance = interact(qr).draggable({
                                listeners: {
                                    start: (event) => {
                                        console.log('🎯 Drag started');
                                        event.target.style.opacity = '0.8';
                                        event.target.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';

                                        console.log(
                                            `Canvas dimensions: ${canvas.width}x${canvas.height}`
                                        );
                                    },
                                    move: (event) => {
                                        const target = event.target;
                                        let x = (parseFloat(target.getAttribute('data-x')) || 0) +
                                            event.dx;
                                        let y = (parseFloat(target.getAttribute('data-y')) || 0) +
                                            event.dy;

                                        // Get current canvas dimensions
                                        const canvasWidth = canvas.width || canvas.offsetWidth;
                                        const canvasHeight = canvas.height || canvas.offsetHeight;
                                        const qrSize = 100; // QR code size

                                        // Apply strict constraints to keep QR within canvas bounds
                                        x = Math.max(0, Math.min(x, canvasWidth - qrSize));
                                        y = Math.max(0, Math.min(y, canvasHeight - qrSize));

                                        target.style.transform = `translate(${x}px, ${y}px)`;
                                        target.setAttribute('data-x', x);
                                        target.setAttribute('data-y', y);

                                        // Update position display with corrected coordinates
                                        this.updatePositionDisplay(x, y);
                                    },
                                    end: (event) => {
                                        console.log('🎯 Drag ended');
                                        event.target.style.opacity = '1';
                                        event.target.style.boxShadow = '';

                                        const x = parseFloat(event.target.getAttribute('data-x')) ||
                                            0;
                                        const y = parseFloat(event.target.getAttribute('data-y')) ||
                                            0;
                                        console.log(`📍 Final QR position: x=${x}, y=${y}`);
                                    }
                                },
                                // Remove complex modifiers that might interfere
                                modifiers: []
                            });

                            // Setup button events
                            this.setupButtons();
                        },

                        setupButtons() {
                            const saveBtn = document.getElementById('save-position-btn');
                            const resetBtn = document.getElementById('reset-position-btn');

                            if (saveBtn) {
                                saveBtn.onclick = () => this.saveQrPosition();
                            }

                            if (resetBtn) {
                                resetBtn.onclick = () => this.resetQrPosition();
                            }
                        },

                        saveQrPosition() {
                            const qr = document.getElementById('qr-overlay');
                            const canvas = document.getElementById('pdf-canvas');
                            if (!qr || !canvas) return;

                            const x = parseFloat(qr.getAttribute('data-x')) || 0;
                            const y = parseFloat(qr.getAttribute('data-y')) || 0;

                            // Convert pixel position to PDF coordinates
                            // PDF coordinate system: (0,0) at bottom-left, Y increases upward
                            // Canvas coordinate system: (0,0) at top-left, Y increases downward
                            const pdfX = x; // X remains the same
                            const pdfY = canvas.height - y - 100; // Flip Y coordinate and account for QR height

                            const rect = qr.getBoundingClientRect();
                            const scale = rect.width;

                            console.log(`💾 Saving QR position:`);
                            console.log(`  Canvas position: x=${x}, y=${y}`);
                            console.log(`  PDF position: x=${pdfX}, y=${pdfY}`);
                            console.log(`  Canvas height: ${canvas.height}`);
                            console.log(`  Scale: ${scale}`);

                            // Use global helper function to dispatch event
                            dispatchLivewireEvent('save-qr-position', {
                                x: pdfX,
                                y: pdfY,
                                scale,
                                canvasHeight: canvas.height,
                                canvasWidth: canvas.width
                            });
                        },

                        resetQrPosition() {
                            const qr = document.getElementById('qr-overlay');
                            const canvas = document.getElementById('pdf-canvas');
                            if (!qr || !canvas) return;

                            console.log('🔄 Resetting QR position');

                            // Position QR at center of canvas, or fallback to safe position
                            const canvasWidth = canvas.width || canvas.offsetWidth || 400;
                            const canvasHeight = canvas.height || canvas.offsetHeight || 300;
                            const qrSize = 100;

                            // Center position with bounds checking
                            const centerX = Math.max(0, (canvasWidth - qrSize) / 2);
                            const centerY = Math.max(0, (canvasHeight - qrSize) / 2);

                            qr.style.transform = `translate(${centerX}px, ${centerY}px)`;
                            qr.setAttribute('data-x', centerX.toString());
                            qr.setAttribute('data-y', centerY.toString());

                            this.updatePositionDisplay(centerX, centerY);
                            console.log(`QR reset to center: x=${centerX}, y=${centerY}`);
                        },

                        updatePositionDisplay(x = 0, y = 0) {
                            const canvas = document.getElementById('pdf-canvas');
                            const positionInfo = document.getElementById('current-position');
                            if (positionInfo && canvas) {
                                // Show both canvas coordinates and PDF coordinates
                                const pdfX = x;
                                const pdfY = canvas.height - y - 100; // Convert to PDF coordinate system
                                positionInfo.innerHTML =
                                    `Canvas: X=${Math.round(x)}, Y=${Math.round(y)} | PDF: X=${Math.round(pdfX)}, Y=${Math.round(pdfY)}`;
                            }
                        },

                        clearCanvas() {
                            const canvas = document.getElementById('pdf-canvas');
                            if (canvas) {
                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                // Reset canvas dimensions
                                canvas.width = 0;
                                canvas.height = 0;
                                console.log('🧹 Canvas cleared by user');
                            }

                            // Clean up QR dragging
                            if (this.qrDragInstance) {
                                this.qrDragInstance.unset();
                                this.qrDragInstance = null;
                            }

                            // Reset QR position
                            const qr = document.getElementById('qr-overlay');
                            if (qr) {
                                qr.style.transform = 'translate(0px, 0px)';
                                qr.setAttribute('data-x', '0');
                                qr.setAttribute('data-y', '0');
                            }

                            this.updatePositionDisplay(0, 0);
                            this.currentPdfUrl = null;
                            this.pdfDocument = null;

                            // Show notification that preview was cleared by user
                            console.log('📢 Preview dibersihkan oleh user');
                        },

                        clearCanvasQuietly() {
                            const canvas = document.getElementById('pdf-canvas');
                            if (canvas) {
                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                // Reset canvas dimensions
                                canvas.width = 0;
                                canvas.height = 0;
                                console.log('🔄 Canvas cleared quietly for new content');
                            }

                            // Clean up QR dragging
                            if (this.qrDragInstance) {
                                this.qrDragInstance.unset();
                                this.qrDragInstance = null;
                            }

                            // Reset QR position
                            const qr = document.getElementById('qr-overlay');
                            if (qr) {
                                qr.style.transform = 'translate(0px, 0px)';
                                qr.setAttribute('data-x', '0');
                                qr.setAttribute('data-y', '0');
                            }

                            this.updatePositionDisplay(0, 0);
                            this.currentPdfUrl = null;
                            this.pdfDocument = null;

                            // No notification - this is internal cleanup
                        },
                    }));
                });

                // Fallback initialization for non-Alpine environments
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Alpine === 'undefined') {
                        console.log('📌 Alpine not found, using fallback initialization');
                        initializeFallback();
                    }
                });

                function initializeFallback() {
                    // Global variables
                    let currentPdfUrl = null;
                    let pdfDocument = null;
                    let qrDragInstance = null;
                    let isLoading = false;
                    let debounceTimer = null;

                    console.log('🚀 Fallback PDF Preview Script Loaded');

                    // Initialize the application
                    function initializeApp() {
                        console.log('🎯 Initializing App (Fallback)');
                        setupPdfPreview();
                        setupEventListeners();
                        setupButtons();
                    }

                    // Setup event listeners
                    function setupEventListeners() {
                        // Livewire hooks
                        document.addEventListener('livewire:navigated', () => {
                            console.log('🔄 Livewire navigated');
                            setTimeout(initializeApp, 100);
                        });

                        // Custom events from Livewire
                        window.addEventListener('preview-updated', (event) => {
                            console.log('📡 Preview updated event:', event.detail);

                            // Clear existing debounce timer
                            if (debounceTimer) {
                                clearTimeout(debounceTimer);
                            }

                            // Debounce to avoid premature clearing
                            debounceTimer = setTimeout(() => {
                                // Get PDF URL from event data
                                const eventData = event.detail[0];
                                if (eventData && eventData.pdfUrl) {
                                    // Only clear if we're loading a different PDF
                                    if (currentPdfUrl !== eventData.pdfUrl) {
                                        console.log('🔄 Loading different PDF, clearing previous');
                                        clearCanvasQuietly(); // Clear without notification
                                    }
                                    setupPdfPreview(eventData.pdfUrl);
                                }
                                // Don't auto-clear if no PDF URL - let user decide
                            }, 300);
                        });

                        // Only trigger when user explicitly clears preview
                        window.addEventListener('preview-cleared', (event) => {
                            console.log('🧹 Preview cleared by user');
                            if (debounceTimer) {
                                clearTimeout(debounceTimer);
                            }
                            clearCanvas(); // This will show the "preview dibersihkan" message
                        });

                        window.addEventListener('position-saved', (event) => {
                            console.log('💾 Position saved event:', event.detail);
                            updatePositionDisplay();
                        });
                    }

                    // Setup PDF preview
                    function setupPdfPreview(eventPdfUrl = null) {
                        console.log('🎯 Setting up PDF preview (Fallback)');

                        // Get PDF URL from event or backend
                        const pdfUrl = eventPdfUrl || @json($this->pdfUrl ?? '');
                        const canvas = document.getElementById('pdf-canvas');

                        console.log('PDF URL:', pdfUrl);

                        if (!pdfUrl || pdfUrl === 'null' || pdfUrl === '') {
                            console.warn('⚠️ PDF URL is empty or invalid');
                            // Don't clear, just return silently
                            return;
                        }

                        if (!canvas) {
                            console.warn('⚠️ Canvas element not found');
                            return;
                        }

                        // Skip if same PDF is already loaded
                        if (currentPdfUrl === pdfUrl && pdfDocument) {
                            console.log('📄 PDF already loaded, setting up QR only');
                            setupQrDragging();
                            return;
                        }

                        console.log('📄 Loading new PDF:', pdfUrl);
                        isLoading = true;
                        currentPdfUrl = pdfUrl;

                        const loadingTask = pdfjsLib.getDocument({
                            url: pdfUrl,
                            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                            cMapPacked: true,
                            verbosity: 0
                        });

                        loadingTask.promise
                            .then(pdf => {
                                console.log('✅ PDF loaded successfully');
                                pdfDocument = pdf;
                                return pdf.getPage(1);
                            })
                            .then(page => {
                                const scale = 1.5;
                                const viewport = page.getViewport({
                                    scale
                                });
                                const context = canvas.getContext('2d');

                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                console.log(`🎨 Rendering PDF to canvas (${canvas.width}x${canvas.height})`);

                                const renderContext = {
                                    canvasContext: context,
                                    viewport: viewport
                                };

                                return page.render(renderContext).promise;
                            })
                            .then(() => {
                                console.log('✅ PDF rendered successfully');
                                isLoading = false;
                                setupQrDragging();
                            })
                            .catch(error => {
                                console.error('❌ Error loading/rendering PDF:', error);
                                isLoading = false;
                                // Only clear on critical errors that user should know about
                                if (error.name !== 'AbortException' && error.name !== 'InvalidPDFException') {
                                    console.error('Critical PDF error, clearing canvas silently');
                                    clearCanvasQuietly();
                                }
                            });
                    }

                    // Setup QR dragging functionality with constraints
                    function setupQrDragging() {
                        const qr = document.getElementById('qr-overlay');
                        const container = document.getElementById('pdf-container');
                        const canvas = document.getElementById('pdf-canvas');

                        if (!qr || !container || !canvas) {
                            console.warn('⚠️ Required elements not found for QR dragging');
                            return;
                        }

                        console.log('🎯 Setting up QR dragging with constraints (Fallback)');

                        // Clean up existing interact instance
                        if (qrDragInstance) {
                            qrDragInstance.unset();
                        }

                        // Reset QR position to center
                        resetQrPosition();

                        // Setup new interact instance with proper canvas boundaries
                        qrDragInstance = interact(qr).draggable({
                            listeners: {
                                start(event) {
                                    console.log('🎯 Drag started');
                                    event.target.style.opacity = '0.8';
                                    event.target.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';

                                    console.log(`Canvas dimensions: ${canvas.width}x${canvas.height}`);
                                },
                                move(event) {
                                    const target = event.target;
                                    let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                    let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                                    // Get current canvas dimensions
                                    const canvasWidth = canvas.width || canvas.offsetWidth;
                                    const canvasHeight = canvas.height || canvas.offsetHeight;
                                    const qrSize = 100; // QR code size

                                    // Apply strict constraints to keep QR within canvas bounds
                                    x = Math.max(0, Math.min(x, canvasWidth - qrSize));
                                    y = Math.max(0, Math.min(y, canvasHeight - qrSize));

                                    target.style.transform = `translate(${x}px, ${y}px)`;
                                    target.setAttribute('data-x', x);
                                    target.setAttribute('data-y', y);

                                    // Update position display with corrected coordinates
                                    updatePositionDisplay(x, y);
                                },
                                end(event) {
                                    console.log('🎯 Drag ended');
                                    event.target.style.opacity = '1';
                                    event.target.style.boxShadow = '';

                                    const x = parseFloat(event.target.getAttribute('data-x')) || 0;
                                    const y = parseFloat(event.target.getAttribute('data-y')) || 0;
                                    console.log(`📍 Final QR position: x=${x}, y=${y}`);
                                }
                            },
                            // Remove complex modifiers that might interfere
                            modifiers: []
                        });
                    }

                    // Setup button functionality
                    function setupButtons() {
                        const saveBtn = document.getElementById('save-position-btn');
                        const resetBtn = document.getElementById('reset-position-btn');

                        if (saveBtn) {
                            saveBtn.onclick = function() {
                                saveQrPosition();
                            };
                        }

                        if (resetBtn) {
                            resetBtn.onclick = function() {
                                resetQrPosition();
                            };
                        }
                    }

                    // Save QR position
                    function saveQrPosition() {
                        const qr = document.getElementById('qr-overlay');
                        const canvas = document.getElementById('pdf-canvas');
                        if (!qr || !canvas) return;

                        const x = parseFloat(qr.getAttribute('data-x')) || 0;
                        const y = parseFloat(qr.getAttribute('data-y')) || 0;

                        // Convert pixel position to PDF coordinates
                        // PDF coordinate system: (0,0) at bottom-left, Y increases upward
                        // Canvas coordinate system: (0,0) at top-left, Y increases downward
                        const pdfX = x; // X remains the same
                        const pdfY = canvas.height - y - 100; // Flip Y coordinate and account for QR height

                        const rect = qr.getBoundingClientRect();
                        const scale = rect.width;

                        console.log(`💾 Saving QR position:`);
                        console.log(`  Canvas position: x=${x}, y=${y}`);
                        console.log(`  PDF position: x=${pdfX}, y=${pdfY}`);
                        console.log(`  Canvas height: ${canvas.height}`);
                        console.log(`  Scale: ${scale}`);

                        // Use global helper function to dispatch event
                        dispatchLivewireEvent('save-qr-position', {
                            x: pdfX,
                            y: pdfY,
                            scale,
                            canvasHeight: canvas.height,
                            canvasWidth: canvas.width
                        });
                    }

                    // Reset QR position
                    function resetQrPosition() {
                        const qr = document.getElementById('qr-overlay');
                        const canvas = document.getElementById('pdf-canvas');
                        if (!qr || !canvas) return;

                        console.log('🔄 Resetting QR position');

                        // Position QR at center of canvas, or fallback to safe position
                        const canvasWidth = canvas.width || canvas.offsetWidth || 400;
                        const canvasHeight = canvas.height || canvas.offsetHeight || 300;
                        const qrSize = 100;

                        // Center position with bounds checking
                        const centerX = Math.max(0, (canvasWidth - qrSize) / 2);
                        const centerY = Math.max(0, (canvasHeight - qrSize) / 2);

                        qr.style.transform = `translate(${centerX}px, ${centerY}px)`;
                        qr.setAttribute('data-x', centerX.toString());
                        qr.setAttribute('data-y', centerY.toString());

                        updatePositionDisplay(centerX, centerY);
                        console.log(`QR reset to center: x=${centerX}, y=${centerY}`);
                    }

                    // Update position display
                    function updatePositionDisplay(x = 0, y = 0) {
                        const canvas = document.getElementById('pdf-canvas');
                        const positionInfo = document.getElementById('current-position');
                        if (positionInfo && canvas) {
                            // Show both canvas coordinates and PDF coordinates
                            const pdfX = x;
                            const pdfY = canvas.height - y - 100; // Convert to PDF coordinate system
                            positionInfo.innerHTML =
                                `Canvas: X=${Math.round(x)}, Y=${Math.round(y)} | PDF: X=${Math.round(pdfX)}, Y=${Math.round(pdfY)}`;
                        }
                    }

                    // Clear canvas (user-triggered)
                    function clearCanvas() {
                        const canvas = document.getElementById('pdf-canvas');
                        if (canvas) {
                            const ctx = canvas.getContext('2d');
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            // Reset canvas dimensions
                            canvas.width = 0;
                            canvas.height = 0;
                            console.log('🧹 Canvas cleared by user');
                        }

                        // Clean up QR dragging
                        if (qrDragInstance) {
                            qrDragInstance.unset();
                            qrDragInstance = null;
                        }

                        // Reset QR position
                        const qr = document.getElementById('qr-overlay');
                        if (qr) {
                            qr.style.transform = 'translate(0px, 0px)';
                            qr.setAttribute('data-x', '0');
                            qr.setAttribute('data-y', '0');
                        }

                        updatePositionDisplay(0, 0);
                        currentPdfUrl = null;
                        pdfDocument = null;

                        // Show notification that preview was cleared by user
                        console.log('📢 Preview dibersihkan oleh user');
                    }

                    // Clear canvas quietly (internal use)
                    function clearCanvasQuietly() {
                        const canvas = document.getElementById('pdf-canvas');
                        if (canvas) {
                            const ctx = canvas.getContext('2d');
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            // Reset canvas dimensions
                            canvas.width = 0;
                            canvas.height = 0;
                            console.log('🔄 Canvas cleared quietly for new content');
                        }

                        // Clean up QR dragging
                        if (qrDragInstance) {
                            qrDragInstance.unset();
                            qrDragInstance = null;
                        }

                        // Reset QR position
                        const qr = document.getElementById('qr-overlay');
                        if (qr) {
                            qr.style.transform = 'translate(0px, 0px)';
                            qr.setAttribute('data-x', '0');
                            qr.setAttribute('data-y', '0');
                        }

                        updatePositionDisplay(0, 0);
                        currentPdfUrl = null;
                        pdfDocument = null;

                        // No notification - this is internal cleanup
                    }

                    // Initialize the app
                    initializeApp();
                }
            </script>
        @endpush

        {{-- Add Alpine.js data attribute --}}
        <div x-data="pdfPreview" style="display: none;"></div>
    </x-filament-panels::page>
