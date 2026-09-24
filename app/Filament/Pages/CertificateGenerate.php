<?php

namespace App\Filament\Pages;

use App\Models\CertificateGenerate as CertificateGenerateRecord;
use App\Models\CertificateTemplate;
use App\Models\CertificateTemplateField;
use App\Services\CertificateGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
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
                            ->directory('certificate-backgrounds')
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
                            ->directory('certificate-backgrounds')
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
                ->modalWidth('6xl')
                ->form([
                    Grid::make(10)->schema([
                        // KOLOM KIRI: input data
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

                            TextInput::make('nama')
                                ->label('Nama')
                                ->required()
                                ->live(onBlur: false)
                                ->visible(fn($get) => $get('mode') === 'manual'),

                            TextInput::make('keterangan')
                                ->label('Keterangan')
                                ->live(onBlur: false)
                                ->visible(fn($get) => $get('mode') === 'manual'),

                            TextInput::make('tempat')
                                ->label('Tempat')
                                ->live(onBlur: false)
                                ->visible(fn($get) => $get('mode') === 'manual'),

                            TextInput::make('tanggal')
                                ->label('Tanggal')
                                ->live(onBlur: false)
                                ->visible(fn($get) => $get('mode') === 'manual'),

                            TextInput::make('tahun')
                                ->label('Tahun')
                                ->live(onBlur: false)
                                ->visible(fn($get) => $get('mode') === 'manual'),

                            FileUpload::make('import_file')
                                ->label('File CSV / Excel')
                                ->helperText(
                                    'Header kolom harus: nama, keterangan, tempat, tanggal, tahun. '
                                        . 'File boleh berisi berapapun baris, tapi hanya ' . CertificateGeneratorService::MAX_ROWS . ' baris pertama yang akan diproses.'
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
                                ->visible(fn($get) => $get('mode') === 'csv'),
                        ])->columnSpan(3),

                        // KOLOM KANAN: preview live — otomatis update mengikuti
                        // input di kiri (template terpilih, nama, keterangan,
                        // tempat, tanggal, tahun), memakai posisi & style
                        // yang sudah diatur lewat "Atur Posisi".
                        ViewField::make('live_preview')
                            ->label('Preview')
                            ->view('filament.pages.certificate-live-preview')
                            ->viewData(fn(Get $get) => $this->buildLivePreviewData($get))
                            ->dehydrated(false)
                            ->columnSpan(7),
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

        $fields = $template->fields()->where('is_placed', true)->get()->keyBy('field_key');

        $mode = $get('mode') ?? 'manual';

        $values = [
            'nama' => $mode === 'manual' ? ($get('nama') ?: 'Nama Peserta') : 'Nama Peserta',
            'keterangan' => $mode === 'manual' ? ($get('keterangan') ?: '') : '',
            'tempat' => $mode === 'manual' ? ($get('tempat') ?: '') : '',
            'tanggal' => $mode === 'manual' ? ($get('tanggal') ?: '') : '',
            'tahun' => $mode === 'manual' ? ($get('tahun') ?: '') : '',
        ];

        return [
            'previewImageUrl' => $previewImageUrl,
            'fields' => $fields,
            'values' => $values,
            'pdfWidthPt' => $template->image_width ?? 0,
        ];
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

        if (! $result['url']) {
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

        $this->dispatch('open-download', url: $result['url']);
    }

    // ==================================================================
    // ATUR POSISI (drag-drop field di dalam modal)
    // ==================================================================
    protected function renderPdfPreviewImage(string $pdfAbs, string $cacheKey): string
    {
        if (! file_exists($pdfAbs)) {
            throw new \RuntimeException("File PDF tidak ditemukan: {$pdfAbs}");
        }

        $previewRelPath = "certificate-previews/{$cacheKey}.png";
        $previewAbsPath = Storage::disk('public')->path($previewRelPath);

        $needsRender = ! file_exists($previewAbsPath)
            || filemtime($pdfAbs) > filemtime($previewAbsPath);

        if ($needsRender) {
            Storage::disk('public')->makeDirectory('certificate-previews');

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
     * @return array<string, array{label: string, x: float, y: float, font_size: int, font_color: string, font_family: string, font_bold: bool, font_underline: bool, text_align: string, is_placed: bool}>
     */
    protected function getFieldsForTemplate(CertificateTemplate $template): array
    {
        $existing = $template->fields()->get()->keyBy('field_key');

        $fields = [];
        foreach (CertificateTemplate::AVAILABLE_FIELDS as $key => $label) {
            $f = $existing->get($key);

            $fields[$key] = [
                'label' => $label,
                'x' => $f ? (float) $f->x : 50,
                'y' => $f ? (float) $f->y : 50,
                'font_size' => $f ? (int) $f->font_size : ($key === 'qrcode' ? 80 : 24),
                'font_color' => $f?->font_color ?? '#000000',
                'font_family' => $f?->font_family ?? 'Helvetica',
                'font_bold' => $f ? (bool) $f->font_bold : false,
                'font_underline' => $f ? (bool) $f->font_underline : false,
                'text_align' => $f?->text_align ?? 'center',
                'is_placed' => $f ? (bool) $f->is_placed : false,
            ];
        }

        return $fields;
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
        return CertificateGenerateRecord::with('template')->latest()->limit(50)->get();
    }
}
