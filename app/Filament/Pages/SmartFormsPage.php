<?php

namespace App\Filament\Pages;

use App\Services\SmartFormsApiService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class SmartFormsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'SmartForms';
    protected static ?string $navigationLabel = 'SmartForms';
    protected static ?string $title = 'SmartForms';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.smart-forms-page';

    public array $transactions = [];
    public array $packages = [];
    public array $discounts = [];
    public int $total = 0;
    public int $totalDiscounts = 0;
    public array $stats = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        try {
            $apiService = new SmartFormsApiService();

            $result = $apiService->getTransactions([], 1, 20);
            $this->transactions = $result['data'] ?? [];
            $this->total = $result['total'] ?? 0;
            $this->stats = $this->calculateStats($this->transactions);

            $this->packages = $apiService->getPackages();

            $discountResult = $apiService->getDiscounts([], 1, 50);
            $this->discounts = $discountResult['data'] ?? [];
            $this->totalDiscounts = $discountResult['total'] ?? 0;
        } catch (\Exception $e) {
            Log::error('❌ [SmartFormsPage] loadData() error', [
                'message' => $e->getMessage(),
            ]);
            $this->transactions = [];
            $this->packages = [];
            $this->discounts = [];
            $this->total = 0;
            $this->totalDiscounts = 0;
            $this->stats = [];
        }
    }

    private function calculateStats(array $transactions): array
    {
        $totalSuccess = 0;
        $totalPending = 0;
        $totalFailed = 0;
        $totalAmount = 0;

        foreach ($transactions as $tx) {
            $amountStr = $tx['amount'] ?? 'Rp 0';
            $amount = (float) preg_replace('/[^0-9]/', '', $amountStr);
            $totalAmount += $amount;

            match ($tx['status'] ?? '') {
                'success'  => $totalSuccess++,
                'pending'  => $totalPending++,
                'failed'   => $totalFailed++,
                default    => null,
            };
        }

        return [
            'total_transactions' => count($transactions),
            'total_success'      => $totalSuccess,
            'total_pending'      => $totalPending,
            'total_failed'       => $totalFailed,
            'total_amount'       => $totalAmount,
        ];
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalDiscounts(): int
    {
        return $this->totalDiscounts;
    }

    public function getActiveDiscountCount(): int
    {
        return count(array_filter($this->discounts, fn($d) => $d['is_active'] ?? false));
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function getTotalAmount(): float
    {
        return $this->stats['total_amount'] ?? 0;
    }

    public function getSuccessCount(): int
    {
        return $this->stats['total_success'] ?? 0;
    }

    public function getPendingCount(): int
    {
        return $this->stats['total_pending'] ?? 0;
    }

    public function getFailedCount(): int
    {
        return $this->stats['total_failed'] ?? 0;
    }

    /* =========================================================
     | ACTIONS (Create) — dipanggil dari headerEnd tiap section tabel
     |=========================================================*/

    public function createPackageAction(): Action
    {
        return Action::make('createPackage')
            ->label('Tambah Paket')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->form($this->packageFormSchema())
            ->action(function (array $data): void {
                $apiService = new SmartFormsApiService();
                $result = $apiService->createPackage($this->normalizePackagePayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Paket berhasil ditambahkan')
                        ->success()
                        ->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menambahkan paket')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function createDiscountAction(): Action
    {
        return Action::make('createDiscount')
            ->label('Tambah Diskon')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->form($this->discountFormSchema())
            ->action(function (array $data): void {
                $apiService = new SmartFormsApiService();
                $result = $apiService->createDiscount($this->normalizeDiscountPayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Diskon berhasil ditambahkan')
                        ->success()
                        ->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menambahkan diskon')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    /* =========================================================
     | ROW ACTIONS (Edit / Delete) — Packages
     |=========================================================*/

    public function editPackageAction(): Action
    {
        return Action::make('editPackage')
            ->label('Ubah')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->link()
            ->size('sm')
            ->form($this->packageFormSchema())
            ->fillForm(function (array $arguments): array {
                $pkg = $arguments['package'] ?? [];
                return [
                    'nama'            => $pkg['nama'] ?? null,
                    'deskripsi'       => $pkg['deskripsi'] ?? null,
                    'harga'           => $pkg['harga'] ?? null,
                    'perBulan'        => $pkg['perBulan'] ?? false,
                    'max_forms'       => $pkg['max_forms'] ?? 0,
                    'max_submissions' => $pkg['max_submissions'] ?? 0,
                    'fitur'           => $pkg['fitur'] ?? [],
                    'populer'         => $pkg['populer'] ?? false,
                    'pro'             => $pkg['pro'] ?? false,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $pkg = $arguments['package'] ?? [];
                $uuid = $pkg['uuid'] ?? null;

                if (!$uuid) {
                    Notification::make()->title('UUID paket tidak ditemukan')->danger()->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $result = $apiService->updatePackage((string) $uuid, $this->normalizePackagePayload($data));

                if ($result !== null) {
                    Notification::make()->title('Paket berhasil diperbarui')->success()->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal memperbarui paket')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function deletePackageAction(): Action
    {
        return Action::make('deletePackage')
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->link()
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading('Hapus Paket')
            ->modalDescription('Apakah Anda yakin ingin menghapus paket ini? Tindakan ini tidak dapat dibatalkan.')
            ->action(function (array $arguments): void {
                $pkg = $arguments['package'] ?? [];
                $uuid = $pkg['uuid'] ?? null;

                if (!$uuid) {
                    Notification::make()->title('UUID paket tidak ditemukan')->danger()->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $success = $apiService->deletePackage((string) $uuid);

                if ($success) {
                    Notification::make()->title('Paket berhasil dihapus')->success()->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menghapus paket')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    /* =========================================================
     | ROW ACTIONS (Edit / Delete) — Discounts
     |=========================================================*/

    public function editDiscountAction(): Action
    {
        return Action::make('editDiscount')
            ->label('Ubah')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->link()
            ->size('sm')
            ->form($this->discountFormSchema())
            ->fillForm(function (array $arguments): array {
                $disc = $arguments['discount'] ?? [];
                return [
                    'code'                  => $disc['code'] ?? null,
                    'name'                  => $disc['name'] ?? null,
                    'type'                  => $disc['type'] ?? 'general',
                    'discount_percent'      => $disc['discount_percent'] ?? 0,
                    'min_purchase'          => $disc['min_purchase'] ?? 0,
                    'max_discount'          => $disc['max_discount'] ?? 0,
                    'applicable_packages'   => $disc['applicable_packages'] ?? [],
                    'usage_limit'           => $disc['usage_limit'] ?? 0,
                    'usage_limit_per_user'  => $disc['usage_limit_per_user'] ?? 1,
                    'is_active'             => $disc['is_active'] ?? true,
                    'starts_at'             => $disc['starts_at'] ?? null,
                    'expires_at'            => $disc['expires_at'] ?? null,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $disc = $arguments['discount'] ?? [];
                $id = $disc['id'] ?? null;

                if (!$id) {
                    Notification::make()->title('ID diskon tidak ditemukan')->danger()->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $result = $apiService->updateDiscount((string) $id, $this->normalizeDiscountPayload($data));

                if ($result !== null) {
                    Notification::make()->title('Diskon berhasil diperbarui')->success()->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal memperbarui diskon')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function deleteDiscountAction(): Action
    {
        return Action::make('deleteDiscount')
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->link()
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading('Hapus Diskon')
            ->modalDescription('Apakah Anda yakin ingin menghapus kode diskon ini? Tindakan ini tidak dapat dibatalkan.')
            ->action(function (array $arguments): void {
                $disc = $arguments['discount'] ?? [];
                $id = $disc['id'] ?? null;

                if (!$id) {
                    Notification::make()->title('ID diskon tidak ditemukan')->danger()->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $success = $apiService->deleteDiscount((string) $id);

                if ($success) {
                    Notification::make()->title('Diskon berhasil dihapus')->success()->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menghapus diskon')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    /* =========================================================
     | FORM SCHEMAS
     |=========================================================*/

    protected function packageFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('nama')
                ->label('Nama Paket')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('harga')
                        ->label('Harga')
                        ->required()
                        ->helperText('Contoh: Rp 99.000'),

                    Forms\Components\Toggle::make('perBulan')
                        ->label('Per Bulan?')
                        ->inline(false),
                ]),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('max_forms')
                        ->label('Maks. Form')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi -1 untuk unlimited'),

                    Forms\Components\TextInput::make('max_submissions')
                        ->label('Maks. Submission')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi -1 untuk unlimited'),
                ]),

            Forms\Components\TagsInput::make('fitur')
                ->label('Fitur')
                ->placeholder('Ketik fitur lalu tekan Enter')
                ->columnSpanFull(),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Toggle::make('populer')
                        ->label('Tandai sebagai Populer')
                        ->inline(false),

                    Forms\Components\Toggle::make('pro')
                        ->label('Tandai sebagai Pro')
                        ->inline(false),
                ]),
        ];
    }

    protected function discountFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Kode Diskon')
                        ->required()
                        ->maxLength(50)
                        ->helperText('Contoh: WELCOME10'),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Diskon')
                        ->required()
                        ->maxLength(255),
                ]),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Tipe')
                        ->options([
                            'general'     => 'Umum (semua paket)',
                            'per_package' => 'Per Paket',
                        ])
                        ->default('general')
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('discount_percent')
                        ->label('Persen Diskon (%)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->required(),
                ]),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('min_purchase')
                        ->label('Minimal Belanja (Rp)')
                        ->numeric()
                        ->default(0),

                    Forms\Components\TextInput::make('max_discount')
                        ->label('Maksimal Potongan (Rp)')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi 0 jika tidak dibatasi'),
                ]),

            Forms\Components\TagsInput::make('applicable_packages')
                ->label('Slug Paket Berlaku')
                ->placeholder('contoh: starter, pro')
                ->visible(fn(Forms\Get $get) => $get('type') === 'per_package')
                ->columnSpanFull(),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('usage_limit')
                        ->label('Batas Total Penggunaan')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi 0 untuk unlimited'),

                    Forms\Components\TextInput::make('usage_limit_per_user')
                        ->label('Batas per User')
                        ->numeric()
                        ->default(1),
                ]),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Mulai Berlaku')
                        ->native(false),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Berlaku Sampai')
                        ->native(false),
                ]),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->columnSpanFull(),
        ];
    }

    /* =========================================================
     | PAYLOAD NORMALIZERS
     |=========================================================*/

    private function normalizePackagePayload(array $data): array
    {
        return [
            'nama'            => $data['nama'] ?? null,
            'deskripsi'       => $data['deskripsi'] ?? null,
            'harga'           => $data['harga'] ?? null,
            'perBulan'        => (bool) ($data['perBulan'] ?? false),
            'max_forms'       => (int) ($data['max_forms'] ?? 0),
            'max_submissions' => (int) ($data['max_submissions'] ?? 0),
            'fitur'           => $data['fitur'] ?? [],
            'populer'         => (bool) ($data['populer'] ?? false),
            'pro'             => (bool) ($data['pro'] ?? false),
        ];
    }

    private function normalizeDiscountPayload(array $data): array
    {
        $type = $data['type'] ?? 'general';

        return [
            'code'                 => $data['code'] ?? null,
            'name'                 => $data['name'] ?? null,
            'type'                 => $type,
            'discount_percent'     => (int) ($data['discount_percent'] ?? 0),
            'min_purchase'         => (int) ($data['min_purchase'] ?? 0),
            'max_discount'         => (int) ($data['max_discount'] ?? 0),
            'applicable_packages'  => $type === 'per_package' ? ($data['applicable_packages'] ?? []) : null,
            'usage_limit'          => (int) ($data['usage_limit'] ?? 0),
            'usage_limit_per_user' => (int) ($data['usage_limit_per_user'] ?? 1),
            'is_active'            => (bool) ($data['is_active'] ?? true),
            'starts_at'            => $data['starts_at'] ?? null,
            'expires_at'           => $data['expires_at'] ?? null,
        ];
    }
}
