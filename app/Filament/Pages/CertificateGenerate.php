<?php

namespace App\Filament\Pages;

use App\Models\CertificateGenerate as CertificateGenerateRecord;
use App\Models\CertificateTemplate;
use App\Models\CertificateTemplateField;
use App\Services\CertificateGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;

class CertificateGenerate extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.certificate-generate';
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Sertifikat';
    private string $gsPath = 'gs';

    public function mount(): void
    {
        $this->checkGhostscript();
    }

    private function checkGhostscript(): void
    {
        $possiblePaths = [
            '/usr/bin/gs',
            '/bin/gs',
            '/usr/local/bin/gs',
            '/usr/local/opt/ghostscript/bin/gs',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->gsPath = $path;
                Log::info('[CertificateGenerate] Ghostscript found at', ['path' => $this->gsPath]);
                return;
            }
        }

        $output = [];
        $code = 0;
        exec('which gs 2>&1', $output, $code);
        if ($code === 0 && ! empty($output[0]) && file_exists(trim($output[0]))) {
            $this->gsPath = trim($output[0]);
            Log::info('[CertificateGenerate] Ghostscript found in PATH', ['path' => $this->gsPath]);
            return;
        }

        $this->gsPath = 'gs';
        Log::warning('[CertificateGenerate] Ghostscript using default PATH, belum tervalidasi', ['path' => $this->gsPath]);
    }

    // ==================================================================
    // TABEL TEMPLATE (bagian atas halaman)
    // ==================================================================
    public function table(Table $table): Table
    {
        return $table
            ->query(CertificateTemplate::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('image_width')
                    ->label('Ukuran Halaman')
                    ->formatStateUsing(fn($record) => $record->image_width && $record->image_height
                        ? round($record->image_width) . ' x ' . round($record->image_height) . ' pt'
                        : '-'),

                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('uploadTemplate')
                    ->label('Upload Sertifikat')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('background_image')
                            ->label('File Template Sertifikat (PDF)')
                            ->helperText('Halaman pertama akan dipakai sebagai dasar sertifikat. Teks & QR code digambar langsung di atasnya (bukan dikonversi ke gambar).')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('certificate/backgrounds')
                            ->visibility('public')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        CertificateTemplate::create($data);

                        Notification::make()
                            ->title('Template berhasil diupload')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->action(fn(CertificateTemplate $record) => $this->mountAction('generate', [
                        'certificate_template_id' => $record->uuid,
                    ])),

                Tables\Actions\Action::make('markFields')
                    ->label('Atur Posisi')
                    ->icon('heroicon-o-map-pin')
                    ->color('warning')
                    ->modalHeading(fn(CertificateTemplate $record) => 'Atur Posisi — ' . $record->name)
                    ->modalContent(function (CertificateTemplate $record) {
                        try {
                            $previewImageUrl = $this->renderTemplatePreviewImage($record);
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal memuat preview PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            $previewImageUrl = null;
                        }

                        return view(
                            'filament.pages.mark-fields-modal',
                            [
                                'record' => $record,
                                'fields' => $this->getFieldsForTemplate($record),
                                'previewImageUrl' => $previewImageUrl,
                            ]
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->modalWidth('7xl')
                    ->extraModalWindowAttributes(['style' => 'max-width: 90vw; max-height: 85vh; overflow: hidden;']),

                Tables\Actions\Action::make('previewPdf')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->url(fn(CertificateTemplate $record) => $record->background_image_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('Ganti File')
                    ->form([
                        TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('background_image')
                            ->label('File PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('certificate/backgrounds')
                            ->visibility('public'),
                    ]),

                Tables\Actions\DeleteAction::make(),
            ]);
    }

    // ==================================================================
    // HEADER ACTION: "Generate Sertifikat" (tombol di atas tabel riwayat)
    // ==================================================================
    public function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Sertifikat')
                ->icon('heroicon-o-document-duplicate')
                ->modalWidth('4xl')
                ->fillForm(fn(): array => [
                    'mode' => 'manual',
                    'mapping' => array_fill(0, 20, null) + [
                        'valid_from' => null,
                        'valid_until' => null,
                    ],
                    'mapping_mode' => [
                        'valid_from' => 'fixed',
                        'valid_until' => 'fixed',
                    ],
                    'fixed_values' => [
                        'deskripsi' => null,
                        'catatan' => null,
                        'valid_from' => null,
                        'valid_until' => null,
                    ],
                ])
                ->form([
                    ViewField::make('live_preview')
                        ->label('Preview')
                        ->view('filament.pages.certificate-live-preview')
                        ->viewData(fn(Get $get) => $this->buildLivePreviewData($get))
                        ->dehydrated(false),

                    // Form input di BAWAH
                    Group::make([
                        Select::make('certificate_template_id')
                            ->label('Template Sertifikat')
                            ->options(CertificateTemplate::pluck('name', 'uuid'))
                            ->required()
                            ->live()
                            ->native(false),

                        Radio::make('mode')
                            ->label('Cara Mengisi Data')
                            ->options([
                                'manual' => 'Input Manual (1 sertifikat)',
                                'csv' => 'Upload CSV / Excel (banyak sertifikat)',
                            ])
                            ->inline()
                            ->default('manual')
                            ->live()
                            ->required(),

                        Section::make('Data Sertifikat')
                            ->description('Tercetak langsung di PDF sertifikat, sesuai posisi yang diatur di "Atur Posisi"')
                            ->visible(fn(Get $get) => $get('mode') === 'manual' && filled($get('certificate_template_id')))
                            ->columns(2)
                            ->schema(function (Get $get): array {
                                $templateId = $get('certificate_template_id');

                                if (! $templateId) {
                                    return [];
                                }

                                $fields = CertificateTemplateField::query()
                                    ->where('certificate_template_id', $templateId)
                                    ->where('field_key', '!=', CertificateTemplate::RESERVED_QRCODE_KEY)
                                    ->where('is_archived', false)
                                    ->orderBy('created_at')
                                    ->get();

                                if ($fields->isEmpty()) {
                                    return [
                                        Placeholder::make('no_fields_notice')
                                            ->label('')
                                            ->content('Template ini belum punya field. Buka "Atur Posisi" dulu untuk menambahkan field lewat panel "Kelola Field".')
                                            ->columnSpanFull(),
                                    ];
                                }

                                return $fields
                                    ->map(fn(CertificateTemplateField $f) => TextInput::make("data.{$f->field_key}")
                                        ->label($f->label)
                                        ->live(onBlur: false))
                                    ->all();
                            }),

                        Section::make('Data Verifikasi (tidak dicetak di sertifikat)')
                            ->description('Hanya tersimpan & tampil di halaman verifikasi publik saat QR di-scan')
                            ->visible(fn($get) => $get('mode') === 'manual')
                            ->columns(2)
                            ->schema([
                                Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                DatePicker::make('valid_from')
                                    ->label('Berlaku Dari')
                                    ->native(false),

                                DatePicker::make('valid_until')
                                    ->label('Berlaku Sampai')
                                    ->native(false),

                                RichEditor::make('catatan')
                                    ->label('Catatan')
                                    ->required()
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'strike',
                                        'underline',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Actions::make([
                            FormAction::make('downloadCsvTemplate')
                                ->label('Download Template Excel/CSV')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('gray')
                                ->action(fn(Get $get) => $this->downloadCsvTemplate($get('certificate_template_id')))
                                ->visible(fn(Get $get) => filled($get('certificate_template_id'))),
                        ])
                            ->visible(fn(Get $get) => $get('mode') === 'csv')
                            ->columnSpanFull(),

                        FileUpload::make('import_file')
                            ->label('File CSV / Excel')
                            ->helperText(
                                'Baris pertama file harus berisi nama kolom (header) — bebas namanya apa saja, '
                                    . 'nanti dicocokkan manual di bawah. Cuma ' . CertificateGeneratorService::MAX_ROWS . ' baris data pertama yang akan diproses.'
                            )
                            ->acceptedFileTypes([
                                'text/csv',
                                'text/plain',
                                'application/csv',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->disk('local')
                            ->directory('imports')
                            ->visibility('private')
                            ->required()
                            ->live()
                            ->visible(fn($get) => $get('mode') === 'csv'),

                        Section::make('Pemetaan Field Cetak')
                            ->key(function (Get $get) {
                                $templateId = $get('certificate_template_id') ?? 'none';
                                $uploaded = $get('import_file');

                                $fileMarker = match (true) {
                                    is_string($uploaded) => $uploaded,
                                    is_object($uploaded) && method_exists($uploaded, 'getFilename') => $uploaded->getFilename(),
                                    is_array($uploaded) => md5(json_encode(array_keys($uploaded))),
                                    default => 'none',
                                };

                                return 'printed-mapping-' . $templateId . '-' . $fileMarker;
                            })
                            ->description('Cocokkan kolom file dengan field yang tercetak di sertifikat. Ditebak otomatis dulu kalau namanya mirip — koreksi kalau salah.')
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && filled($get('certificate_template_id')) && filled($get('import_file')))
                            ->columns(2)
                            ->schema(fn(Get $get) => $this->buildPrintedFieldMappingSchema($get))
                            ->columnSpanFull(),

                        Textarea::make('fixed_values.deskripsi')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(3)
                            ->helperText(fn(Get $get) => $this->placeholderHint($get))
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && filled($get('certificate_template_id')))
                            ->columnSpanFull(),

                        RichEditor::make('fixed_values.catatan')
                            ->label('Catatan')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'strike',
                                'underline',
                            ])
                            ->helperText(fn(Get $get) => $this->placeholderHint($get))
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && filled($get('certificate_template_id')))
                            ->columnSpanFull(),

                        Radio::make('mapping_mode.valid_from')
                            ->label('Berlaku Dari')
                            ->options([
                                'fixed' => 'Sama untuk semua sertifikat di batch ini',
                                'column' => 'Per baris (ambil dari kolom file)',
                            ])
                            ->default('fixed')
                            ->live()
                            ->visible(fn(Get $get) => $get('mode') === 'csv')
                            ->columnSpanFull(),

                        Select::make('mapping.valid_from')
                            ->label('Kolom untuk "Berlaku Dari"')
                            ->options(fn(Get $get) => $this->fileHeaderOptions($get))
                            ->searchable()
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && $get('mapping_mode.valid_from') === 'column'),

                        DatePicker::make('fixed_values.valid_from')
                            ->native(false)
                            ->label('Nilai "Berlaku Dari" (berlaku ke semua)')
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && $get('mapping_mode.valid_from') === 'fixed')
                            ->columnSpanFull(),

                        Radio::make('mapping_mode.valid_until')
                            ->label('Berlaku Sampai')
                            ->options([
                                'fixed' => 'Sama untuk semua sertifikat di batch ini',
                                'column' => 'Per baris (ambil dari kolom file)',
                            ])
                            ->default('fixed')
                            ->live()
                            ->visible(fn(Get $get) => $get('mode') === 'csv')
                            ->columnSpanFull(),

                        Select::make('mapping.valid_until')
                            ->label('Kolom untuk "Berlaku Sampai"')
                            ->options(fn(Get $get) => $this->fileHeaderOptions($get))
                            ->searchable()
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && $get('mapping_mode.valid_until') === 'column'),

                        DatePicker::make('fixed_values.valid_until')
                            ->native(false)
                            ->label('Nilai "Berlaku Sampai" (berlaku ke semua)')
                            ->visible(fn(Get $get) => $get('mode') === 'csv' && $get('mapping_mode.valid_until') === 'fixed')
                            ->columnSpanFull(),
                    ]),
                ])
                ->action(fn(array $data) => $this->runGenerate($data)),
        ];
    }

    protected function buildLivePreviewData(Get $get): array
    {
        $empty = [
            'previewImageUrl' => null,
            'fields' => collect(),
            'values' => [],
            'pdfWidthPt' => 0,
        ];

        $templateId = $get('certificate_template_id');

        if (! $templateId) {
            return $empty;
        }

        $template = CertificateTemplate::with('fields')->find($templateId);

        if (! $template) {
            return $empty;
        }

        try {
            $previewImageUrl = $this->renderTemplatePreviewImage($template);
        } catch (\Throwable $e) {
            $previewImageUrl = null;
        }

        $fields = $template->fields()
            ->where('is_placed', true)
            ->where('is_archived', false)
            ->get()
            ->keyBy('field_key');

        $mode = $get('mode') ?? 'manual';

        $values = [];
        foreach ($fields as $key => $field) {
            if ($key === CertificateTemplate::RESERVED_QRCODE_KEY) {
                continue;
            }

            $values[$key] = $mode === 'manual'
                ? ($get("data.{$key}") ?: $field->label)
                : '';
        }

        return [
            'previewImageUrl' => $previewImageUrl,
            'fields' => $fields,
            'values' => $values,
            'pdfWidthPt' => $template->image_width ?? 0,
        ];
    }

    // ==================================================================
    // PENCOCOKAN KOLOM (mode CSV/Excel) — BARU
    // ==================================================================

    protected function buildPrintedFieldMappingSchema(Get $get): array
    {
        $templateId = $get('certificate_template_id');
        $uploaded = $get('import_file');

        $realPath = $this->resolveUploadedFilePath($uploaded);

        if (! $realPath || ! file_exists($realPath)) {
            return [
                Placeholder::make('mapping_no_file')
                    ->label('')
                    ->content('Upload file dulu untuk mencocokkan kolom.')
                    ->columnSpanFull(),
            ];
        }

        try {
            $headers = app(CertificateGeneratorService::class)->readHeaders($realPath);
        } catch (\Throwable $e) {
            return [
                Placeholder::make('mapping_read_error')
                    ->label('')
                    ->content('Gagal membaca file: ' . $e->getMessage())
                    ->columnSpanFull(),
            ];
        }

        if (empty($headers)) {
            return [
                Placeholder::make('mapping_empty_headers')
                    ->label('')
                    ->content('File tidak punya baris header / kosong.')
                    ->columnSpanFull(),
            ];
        }

        $headerOptions = array_combine($headers, $headers);

        $printedFields = CertificateTemplateField::query()
            ->where('certificate_template_id', $templateId)
            ->where('field_key', '!=', CertificateTemplate::RESERVED_QRCODE_KEY)
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get()
            ->values();

        if ($printedFields->isEmpty()) {
            return [
                Placeholder::make('mapping_no_fields')
                    ->label('')
                    ->content('Template ini belum punya field tercetak. Buka "Atur Posisi" dulu untuk menambahkan field.')
                    ->columnSpanFull(),
            ];
        }

        $schema = [];
        foreach ($printedFields as $i => $f) {
            $schema[] = Select::make("mapping.{$i}")
                ->label('Kolom untuk "' . $f->label . '" (dicetak)')
                ->options($headerOptions)
                ->default($this->guessColumnMatch($f->label, $headers))
                ->searchable()
                ->required();
        }

        return $schema;
    }

    protected function fileHeaderOptions(Get $get): array
    {
        $realPath = $this->resolveUploadedFilePath($get('import_file'));

        if (! $realPath || ! file_exists($realPath)) {
            return [];
        }

        try {
            $headers = app(CertificateGeneratorService::class)->readHeaders($realPath);
        } catch (\Throwable) {
            return [];
        }

        return array_combine($headers, $headers);
    }

    protected function placeholderHint(Get $get): string
    {
        $templateId = $get('certificate_template_id');

        if (! $templateId) {
            return '';
        }

        $printedFields = CertificateTemplateField::query()
            ->where('certificate_template_id', $templateId)
            ->where('field_key', '!=', CertificateTemplate::RESERVED_QRCODE_KEY)
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get();

        if ($printedFields->isEmpty()) {
            return 'Belum ada field tercetak di template ini untuk dijadikan variabel.';
        }

        return 'Variabel yang bisa dipakai: '
            . $printedFields->map(fn($f) => '{' . $f->field_key . '}')->implode(', ')
            . ' — otomatis keganti sesuai data tiap baris.';
    }

    protected function resolveUploadedFilePath(mixed $uploaded): ?string
    {
        if (is_array($uploaded)) {
            $uploaded = array_values($uploaded)[0] ?? null;
        }

        if (! $uploaded) {
            return null;
        }

        if (is_object($uploaded) && method_exists($uploaded, 'getRealPath')) {
            return $uploaded->getRealPath() ?: null;
        }

        if (is_string($uploaded)) {
            return Storage::disk('local')->path($uploaded);
        }

        return null;
    }

    protected function guessColumnMatch(string $label, array $headers): ?string
    {
        $normalize = fn(string $s): string => strtolower(preg_replace('/[^a-z0-9]/i', '', $s));
        $target = $normalize($label);

        foreach ($headers as $header) {
            if ($normalize($header) === $target) {
                return $header;
            }
        }

        return null;
    }

    protected function downloadCsvTemplate(?string $templateId)
    {
        if (! $templateId) {
            Notification::make()
                ->title('Pilih template dulu sebelum download contoh file')
                ->warning()
                ->send();
            return;
        }

        $template = CertificateTemplate::with('fields')->find($templateId);

        if (! $template) {
            Notification::make()
                ->title('Template tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $printedFields = $template->fields()
            ->where('field_key', '!=', CertificateTemplate::RESERVED_QRCODE_KEY)
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get();

        if ($printedFields->isEmpty()) {
            Notification::make()
                ->title('Template ini belum punya field tercetak')
                ->body('Buka "Atur Posisi" dulu untuk menambahkan field, sebelum download contoh file.')
                ->warning()
                ->send();
            return;
        }

        $headers = $printedFields->pluck('label')
            ->push('Berlaku Dari')
            ->push('Berlaku Sampai')
            ->all();

        $example = $printedFields->map(fn($f) => 'Contoh ' . $f->label)
            ->push('2026-01-01')
            ->push('2027-01-01')
            ->all();

        $filename = 'template_' . Str::slug($template->name) . '.csv';

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 supaya karakter non-ASCII (jika ada) tampil benar
            // saat file dibuka langsung di Excel.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, $example);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function runGenerate(array $data): void
    {
        $template = CertificateTemplate::with('fields')->findOrFail($data['certificate_template_id']);

        if (! $template->background_image) {
            Notification::make()
                ->title('Template belum punya file PDF')
                ->danger()
                ->send();
            return;
        }

        $service = app(CertificateGeneratorService::class);

        try {
            $result = $service->generate(
                $template,
                $data['mode'],
                $data,
                $data['mode'] === 'csv' ? Storage::disk('local')->path($data['import_file']) : null,
            );
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal generate')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        if ($result['generate']->total_success === 0) {
            Notification::make()
                ->title('Semua data gagal diproses')
                ->body(implode('; ', $result['failedRows']))
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title($result['generate']->total_success . ' sertifikat berhasil dibuat')
            ->body(! empty($result['failedRows']) ? (count($result['failedRows']) . ' baris gagal: ' . implode('; ', $result['failedRows'])) : null)
            ->success()
            ->send();

        if ($result['url']) {
            $this->dispatch('open-download', url: $result['url']);
        }
    }

    // ==================================================================
    // ATUR POSISI (drag-drop field di dalam modal)
    // ==================================================================
    protected function renderPdfPreviewImage(string $pdfAbs, string $cacheKey): string
    {
        if (! file_exists($pdfAbs)) {
            throw new \RuntimeException("File PDF tidak ditemukan: {$pdfAbs}");
        }

        $previewRelPath = "certificate/previews/{$cacheKey}.png";
        $previewAbsPath = Storage::disk('public')->path($previewRelPath);

        $needsRender = ! file_exists($previewAbsPath)
            || filemtime($pdfAbs) > filemtime($previewAbsPath);

        if ($needsRender) {
            Storage::disk('public')->makeDirectory('certificate/previews');

            $command = sprintf(
                '%s -dQUIET -dBATCH -dNOPAUSE -dNOPROMPT '
                    . '-sDEVICE=png16m -r150 '
                    . '-dFirstPage=1 -dLastPage=1 '
                    . '-sOutputFile=%s %s 2>&1',
                $this->gsPath,
                escapeshellarg($previewAbsPath),
                escapeshellarg($pdfAbs)
            );

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 127 && $this->gsPath !== 'gs') {
                // fallback: coba pakai "gs" polos dari PATH
                $fallbackCommand = str_replace(escapeshellarg($this->gsPath), 'gs', $command);
                exec($fallbackCommand, $output, $returnCode);
            }

            if (! file_exists($previewAbsPath) || filesize($previewAbsPath) === 0) {
                Log::error('[CertificateGenerate] Gagal render preview PDF ke PNG', [
                    'cmd' => $command,
                    'output' => implode("\n", $output),
                    'returnCode' => $returnCode,
                ]);

                throw new \RuntimeException(
                    'Gagal me-render preview PDF. Pastikan Ghostscript (gs) terpasang di server. Output: '
                        . implode(' | ', $output)
                );
            }
        }

        // tambahkan query string versi biar browser tidak pakai cache lama
        return Storage::disk('public')->url($previewRelPath) . '?v=' . filemtime($previewAbsPath);
    }
    protected function renderTemplatePreviewImage(CertificateTemplate $template): string
    {
        $pdfAbs = Storage::disk('public')->path($template->background_image);

        return $this->renderPdfPreviewImage($pdfAbs, $template->uuid);
    }

    /**
     * @return array<string, array{label: string, x: float, y: float, font_size: int, font_color: string, font_family: string, font_bold: bool, font_underline: bool, text_align: string, is_placed: bool, usage_count: int}>
     */
    protected function getFieldsForTemplate(CertificateTemplate $template): array
    {
        // BARU: field sekarang custom per-template, langsung ambil dari DB
        // (bukan loop constant AVAILABLE_FIELDS lagi). Field yang sudah
        // diarsipkan gak ditampilkan di "Atur Posisi".
        $existing = $template->fields()
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get();

        $fields = [];
        foreach ($existing as $f) {
            $fields[$f->field_key] = [
                'uuid' => $f->uuid,
                'label' => $f->label,
                'x' => (float) $f->x,
                'y' => (float) $f->y,
                'font_size' => (int) $f->font_size,
                'font_color' => $f->font_color,
                'font_family' => $f->font_family,
                'font_bold' => (bool) $f->font_bold,
                'font_underline' => (bool) $f->font_underline,
                'text_align' => $f->text_align,
                'is_placed' => (bool) $f->is_placed,
                'usage_count' => (int) $f->usage_count,
            ];
        }

        return $fields;
    }

    // ==================================================================
    // KELOLA FIELD — CRUD field custom per template, digabung
    // di dalam modal "Atur Posisi".
    // ==================================================================

    public function addCustomField(string $templateId, string $label): void
    {
        $label = trim($label);

        if ($label === '') {
            Notification::make()
                ->title('Label field wajib diisi')
                ->danger()
                ->send();
            return;
        }

        $baseKey = \Illuminate\Support\Str::slug($label, '_');
        if ($baseKey === '') {
            $baseKey = 'field';
        }

        $archived = CertificateTemplateField::where('certificate_template_id', $templateId)
            ->where('field_key', $baseKey)
            ->where('is_archived', true)
            ->first();

        if ($archived) {
            $archived->update([
                'label' => $label,
                'is_archived' => false,
                'is_placed' => false,
            ]);

            $this->dispatch('field-added', templateId: $templateId, key: $archived->field_key, field: [
                'uuid' => $archived->uuid,
                'label' => $archived->label,
                'x' => (float) $archived->x,
                'y' => (float) $archived->y,
                'font_size' => (int) $archived->font_size,
                'font_color' => $archived->font_color,
                'font_family' => $archived->font_family,
                'font_bold' => (bool) $archived->font_bold,
                'font_underline' => (bool) $archived->font_underline,
                'text_align' => $archived->text_align,
                'is_placed' => false,
                'usage_count' => (int) $archived->usage_count,
            ]);

            Notification::make()
                ->title('Field "' . $label . '" diaktifkan kembali')
                ->success()
                ->send();
            return;
        }

        $key = $baseKey;
        $suffix = 1;
        while (
            CertificateTemplateField::where('certificate_template_id', $templateId)
            ->where('field_key', $key)
            ->where('is_archived', false)
            ->exists()
        ) {
            $suffix++;
            $key = $baseKey . '_' . $suffix;
        }

        $field = CertificateTemplateField::create([
            'certificate_template_id' => $templateId,
            'field_key' => $key,
            'label' => $label,
            'x' => 50,
            'y' => 50,
            'font_size' => 24,
            'font_color' => '#000000',
            'font_family' => 'Helvetica',
            'font_bold' => false,
            'font_underline' => false,
            'text_align' => 'center',
            'is_placed' => false,
            'usage_count' => 0,
            'is_archived' => false,
        ]);

        $this->dispatch('field-added', templateId: $templateId, key: $key, field: [
            'uuid' => $field->uuid,
            'label' => $field->label,
            'x' => (float) $field->x,
            'y' => (float) $field->y,
            'font_size' => (int) $field->font_size,
            'font_color' => $field->font_color,
            'font_family' => $field->font_family,
            'font_bold' => (bool) $field->font_bold,
            'font_underline' => (bool) $field->font_underline,
            'text_align' => $field->text_align,
            'is_placed' => false,
            'usage_count' => 0,
        ]);

        Notification::make()
            ->title('Field "' . $label . '" ditambahkan')
            ->success()
            ->send();
    }

    /**
     * Tambah field QR Code — reserved, field_key selalu literal "qrcode",
     * cuma boleh ada 1 per template YANG AKTIF. Kalau ketemu QR yang
     * diarsipkan, di-restore; kalau ketemu QR yang masih aktif, ditolak.
     */
    public function addQrField(string $templateId): void
    {
        $existingQr = CertificateTemplateField::where('certificate_template_id', $templateId)
            ->where('field_key', CertificateTemplate::RESERVED_QRCODE_KEY)
            ->first();

        if ($existingQr && ! $existingQr->is_archived) {
            Notification::make()
                ->title('QR Code sudah ada di template ini')
                ->warning()
                ->send();
            return;
        }

        if ($existingQr && $existingQr->is_archived) {
            $existingQr->update([
                'is_archived' => false,
                'is_placed' => false,
            ]);

            $this->dispatch('field-added', templateId: $templateId, key: CertificateTemplate::RESERVED_QRCODE_KEY, field: [
                'uuid' => $existingQr->uuid,
                'label' => $existingQr->label,
                'x' => (float) $existingQr->x,
                'y' => (float) $existingQr->y,
                'font_size' => (int) $existingQr->font_size,
                'font_color' => $existingQr->font_color,
                'font_family' => $existingQr->font_family,
                'font_bold' => (bool) $existingQr->font_bold,
                'font_underline' => (bool) $existingQr->font_underline,
                'text_align' => $existingQr->text_align,
                'is_placed' => false,
                'usage_count' => (int) $existingQr->usage_count,
            ]);

            Notification::make()
                ->title('QR Code diaktifkan kembali')
                ->success()
                ->send();
            return;
        }

        $field = CertificateTemplateField::create([
            'certificate_template_id' => $templateId,
            'field_key' => CertificateTemplate::RESERVED_QRCODE_KEY,
            'label' => 'QR Code',
            'x' => 50,
            'y' => 50,
            'font_size' => 80,
            'font_color' => '#000000',
            'font_family' => 'Helvetica',
            'font_bold' => false,
            'font_underline' => false,
            'text_align' => 'center',
            'is_placed' => false,
            'usage_count' => 0,
            'is_archived' => false,
        ]);

        $this->dispatch('field-added', templateId: $templateId, key: CertificateTemplate::RESERVED_QRCODE_KEY, field: [
            'uuid' => $field->uuid,
            'label' => $field->label,
            'x' => (float) $field->x,
            'y' => (float) $field->y,
            'font_size' => (int) $field->font_size,
            'font_color' => $field->font_color,
            'font_family' => $field->font_family,
            'font_bold' => (bool) $field->font_bold,
            'font_underline' => (bool) $field->font_underline,
            'text_align' => $field->text_align,
            'is_placed' => false,
            'usage_count' => 0,
        ]);

        Notification::make()
            ->title('QR Code ditambahkan')
            ->success()
            ->send();
    }

    public function deleteOrArchiveField(string $fieldUuid): void
    {
        $field = CertificateTemplateField::findOrFail($fieldUuid);
        $key = $field->field_key;
        $templateId = $field->certificate_template_id;

        if (! $field->canBeDeleted()) {
            $field->update([
                'is_archived' => true,
                'is_placed' => false,
            ]);

            $this->dispatch('field-removed', templateId: $templateId, key: $key);

            Notification::make()
                ->title('Field diarsipkan')
                ->body('Field "' . $field->label . '" sudah pernah dipakai di ' . $field->usage_count . ' sertifikat, jadi gak bisa dihapus permanen — sudah diarsipkan/disembunyikan.')
                ->warning()
                ->send();
            return;
        }

        $label = $field->label;
        $field->delete();

        $this->dispatch('field-removed', templateId: $templateId, key: $key);

        Notification::make()
            ->title('Field "' . $label . '" dihapus')
            ->success()
            ->send();
    }

    public function saveFields(string $templateId, array $fields): void
    {
        foreach ($fields as $key => $field) {
            CertificateTemplateField::updateOrCreate(
                [
                    'certificate_template_id' => $templateId,
                    'field_key' => $key,
                ],
                [
                    'label' => $field['label'],
                    'x' => round((float) $field['x'], 2),
                    'y' => round((float) $field['y'], 2),
                    'font_size' => (int) $field['font_size'],
                    'font_color' => $field['font_color'],
                    'font_family' => $field['font_family'] ?? 'Helvetica',
                    'font_bold' => (bool) ($field['font_bold'] ?? false),
                    'font_underline' => (bool) ($field['font_underline'] ?? false),
                    'text_align' => $field['text_align'] ?? 'center',
                    'is_placed' => (bool) ($field['is_placed'] ?? false),
                ]
            );
        }

        Notification::make()
            ->title('Posisi berhasil disimpan')
            ->success()
            ->send();
    }

    // ==================================================================
    // TABEL RIWAYAT GENERATE (bagian bawah halaman, dirender manual di Blade)
    // ==================================================================

    public function deleteGenerate(string $uuid): void
    {
        $record = CertificateGenerateRecord::findOrFail($uuid);

        if ($record->file_path) {
            Storage::disk('public')->delete($record->file_path);
        }

        $record->delete();

        Notification::make()
            ->title('Riwayat dihapus')
            ->success()
            ->send();
    }

    public function getGeneratesProperty()
    {
        return CertificateGenerateRecord::with(['template', 'items'])->latest()->limit(50)->get();
    }
}
