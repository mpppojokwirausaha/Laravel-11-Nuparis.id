<?php

namespace App\Filament\Pages;

use App\Services\SmartFormsApiService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    public array $users = [];
    public int $total = 0;
    public int $totalDiscounts = 0;
    public int $totalUsers = 0;
    public array $stats = [];

    public string $searchTransaction = '';
    public string $searchPackage = '';
    public string $searchDiscount = '';
    public string $searchUser = '';

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

            $userResult = $apiService->getUsers([], 1, 50);
            $this->users = $userResult['data'] ?? [];
            $this->totalUsers = $userResult['total'] ?? 0;
        } catch (\Exception $e) {
            Log::error('❌ [SmartFormsPage] loadData() error', [
                'message' => $e->getMessage(),
            ]);
            $this->transactions = [];
            $this->packages = [];
            $this->discounts = [];
            $this->users = [];
            $this->total = 0;
            $this->totalDiscounts = 0;
            $this->totalUsers = 0;
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
                'success' => $totalSuccess++,
                'pending' => $totalPending++,
                'failed'  => $totalFailed++,
                default   => null,
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
        if (trim($this->searchTransaction) === '') {
            return $this->transactions;
        }

        return array_values(array_filter($this->transactions, function ($tx) {
            return $this->matchesSearch($this->searchTransaction, [
                $tx['order_id'] ?? '',
                $tx['user']['name'] ?? '',
                $tx['user']['email'] ?? '',
                $tx['package']['name'] ?? '',
                $tx['discount']['code'] ?? '',
                $tx['status'] ?? '',
            ]);
        }));
    }

    public function getPackages(): array
    {
        if (trim($this->searchPackage) === '') {
            return $this->packages;
        }

        return array_values(array_filter($this->packages, function ($pkg) {
            return $this->matchesSearch($this->searchPackage, [
                $pkg['nama'] ?? '',
                $pkg['deskripsi'] ?? '',
                $pkg['harga'] ?? '',
                implode(' ', $pkg['fitur'] ?? []),
            ]);
        }));
    }

    public function getDiscounts(): array
    {
        if (trim($this->searchDiscount) === '') {
            return $this->discounts;
        }

        return array_values(array_filter($this->discounts, function ($disc) {
            return $this->matchesSearch($this->searchDiscount, [
                $disc['code'] ?? '',
                $disc['name'] ?? '',
                $disc['type'] ?? '',
                implode(' ', $disc['applicable_packages'] ?? []),
            ]);
        }));
    }

    public function getUsers(): array
    {
        if (trim($this->searchUser) === '') {
            return $this->users;
        }

        return array_values(array_filter($this->users, function ($user) {
            return $this->matchesSearch($this->searchUser, [
                $user['name'] ?? '',
                $user['email'] ?? '',
                $user['phone'] ?? '',
                $user['subscription']['package_name'] ?? '',
                $user['subscription']['status_label'] ?? '',
            ]);
        }));
    }

    /**
     * Get mapping slug → nama paket dari data packages yang sudah di-load
     */
    public function getPackageNameMap(): array
    {
        $map = [];
        foreach ($this->packages as $pkg) {
            $slug = $pkg['slug'] ?? null;
            $name = $pkg['nama'] ?? null;
            if ($slug && $name) {
                $map[$slug] = $name;
            }
        }
        return $map;
    }

    private function matchesSearch(string $term, array $fields): bool
    {
        $term = mb_strtolower(trim($term));

        foreach ($fields as $field) {
            if (str_contains(mb_strtolower((string) $field), $term)) {
                return true;
            }
        }

        return false;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalDiscounts(): int
    {
        return $this->totalDiscounts;
    }

    public function getTotalUsers(): int
    {
        return $this->totalUsers;
    }

    public function getActiveDiscountCount(): int
    {
        return count(array_filter($this->discounts, fn($d) => $d['is_active'] ?? false));
    }

    public function getActiveUserCount(): int
    {
        return count(array_filter($this->users, fn($u) => empty($u['deleted_at'])));
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
     | ACTIONS (Create)
     |=========================================================*/

    public function createPackageAction(): Action
    {
        return Action::make('createPackage')
            ->label('Tambah Paket')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->closeModalByClickingAway(false)
            ->modalHeading('Tambah Paket Baru')
            ->modalDescription('Isi data paket baru di bawah ini.')
            ->form($this->packageFormSchema())
            ->action(function (array $data): void {
                $apiService = new SmartFormsApiService();
                $result = $apiService->createPackage($this->normalizePackagePayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Paket berhasil ditambahkan')
                        ->body('Paket ' . ($data['nama'] ?? '') . ' telah berhasil dibuat.')
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
            ->closeModalByClickingAway(false)
            ->modalHeading('Tambah Diskon Baru')
            ->modalDescription('Isi data kode diskon baru di bawah ini.')
            ->form($this->discountFormSchema())
            ->action(function (array $data): void {
                $apiService = new SmartFormsApiService();
                $result = $apiService->createDiscount($this->normalizeDiscountPayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Diskon berhasil ditambahkan')
                        ->body('Kode diskon ' . ($data['code'] ?? '') . ' telah berhasil dibuat.')
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

    public function createUserAction(): Action
    {
        return Action::make('createUser')
            ->label('Tambah User')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->closeModalByClickingAway(false)
            ->modalHeading('Tambah User Baru')
            ->modalDescription('Isi data user baru di bawah ini.')
            ->form($this->userFormSchema(isCreate: true))
            ->action(function (array $data): void {
                $apiService = new SmartFormsApiService();
                $result = $apiService->createUser($this->normalizeUserPayload($data, isCreate: true));

                if ($result !== null) {
                    Notification::make()
                        ->title('User berhasil ditambahkan')
                        ->body('User ' . ($data['name'] ?? '') . ' telah berhasil dibuat.')
                        ->success()
                        ->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menambahkan user')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    /* =========================================================
     | ROW ACTIONS — Packages (HARD DELETE)
     |=========================================================*/

    public function editPackageAction(): Action
    {
        return Action::make('editPackage')
            ->label('Ubah')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->link()
            ->size('sm')
            ->closeModalByClickingAway(false)
            ->modalHeading('Ubah Paket')
            ->modalDescription('Edit data paket di bawah ini.')
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
                $pkg  = $arguments['package'] ?? [];
                $uuid = $pkg['uuid'] ?? null;
                $nama = $pkg['nama'] ?? 'Paket';

                if (!$uuid) {
                    Notification::make()
                        ->title('UUID paket tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $result = $apiService->updatePackage((string) $uuid, $this->normalizePackagePayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Paket berhasil diperbarui')
                        ->body("Paket \"{$nama}\" telah berhasil diperbarui.")
                        ->success()
                        ->send();
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
            ->closeModalByClickingAway(false)
            ->requiresConfirmation()
            ->modalHeading('Hapus Paket')
            ->modalDescription(function (array $arguments): string {
                $pkg = $arguments['package'] ?? [];
                $nama = $pkg['nama'] ?? 'Paket';
                return "Apakah Anda yakin ingin menghapus paket \"{$nama}\"? Masukkan password Anda untuk mengkonfirmasi penghapusan.";
            })
            ->modalSubmitActionLabel('Ya, Hapus')
            ->modalCancelActionLabel('Batal')
            ->form([
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!Hash::check($value, Auth::user()->password)) {
                                    $fail('Password yang Anda masukkan salah.');
                                }
                            };
                        },
                    ]),
            ])
            ->action(function (array $data, array $arguments): void {
                $pkg = $arguments['package'] ?? [];
                $uuid = $pkg['uuid'] ?? null;
                $nama = $pkg['nama'] ?? 'Paket';

                if (!$uuid) {
                    Notification::make()
                        ->title('UUID paket tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $success = $apiService->deletePackage((string) $uuid);

                if ($success) {
                    Notification::make()
                        ->title('Paket berhasil dihapus')
                        ->body("Paket \"{$nama}\" telah berhasil dihapus.")
                        ->success()
                        ->send();
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
     | ROW ACTIONS — Discounts (SOFT DELETE)
     |=========================================================*/

    public function editDiscountAction(): Action
    {
        return Action::make('editDiscount')
            ->label('Ubah')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->link()
            ->size('sm')
            ->closeModalByClickingAway(false)
            ->modalHeading('Ubah Diskon')
            ->modalDescription('Edit data kode diskon di bawah ini.')
            ->form($this->discountFormSchema())
            ->fillForm(function (array $arguments): array {
                $disc = $arguments['discount'] ?? [];
                return [
                    'code'                 => $disc['code'] ?? null,
                    'name'                 => $disc['name'] ?? null,
                    'type'                 => $disc['type'] ?? 'general',
                    'discount_percent'     => $disc['discount_percent'] ?? 0,
                    'min_purchase'         => $disc['min_purchase'] ?? 0,
                    'max_discount'         => $disc['max_discount'] ?? 0,
                    'applicable_packages'  => $disc['applicable_packages'] ?? [],
                    'usage_limit'          => $disc['usage_limit'] ?? 0,
                    'usage_limit_per_user' => $disc['usage_limit_per_user'] ?? 1,
                    'is_active'            => $disc['is_active'] ?? true,
                    'starts_at'            => $disc['starts_at'] ?? null,
                    'expires_at'           => $disc['expires_at'] ?? null,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $disc = $arguments['discount'] ?? [];
                $id   = $disc['id'] ?? null;
                $code = $disc['code'] ?? 'Diskon';

                if (!$id) {
                    Notification::make()
                        ->title('ID diskon tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $result = $apiService->updateDiscount((string) $id, $this->normalizeDiscountPayload($data));

                if ($result !== null) {
                    Notification::make()
                        ->title('Diskon berhasil diperbarui')
                        ->body("Kode diskon \"{$code}\" telah berhasil diperbarui.")
                        ->success()
                        ->send();
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
            ->closeModalByClickingAway(false)
            ->requiresConfirmation()
            ->modalHeading('Hapus Diskon')
            ->modalDescription(function (array $arguments): string {
                $disc = $arguments['discount'] ?? [];
                $code = $disc['code'] ?? 'Diskon';
                return "Apakah Anda yakin ingin menghapus kode diskon \"{$code}\"? Masukkan password Anda untuk mengkonfirmasi penghapusan.";
            })
            ->modalSubmitActionLabel('Ya, Hapus')
            ->modalCancelActionLabel('Batal')
            ->form([
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!Hash::check($value, Auth::user()->password)) {
                                    $fail('Password yang Anda masukkan salah.');
                                }
                            };
                        },
                    ]),
            ])
            ->action(function (array $data, array $arguments): void {
                $disc = $arguments['discount'] ?? [];
                $id   = $disc['id'] ?? null;
                $code = $disc['code'] ?? 'Diskon';

                if (!$id) {
                    Notification::make()
                        ->title('ID diskon tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                // SOFT DELETE untuk diskon
                $success = $apiService->deleteDiscount((string) $id);

                if ($success) {
                    Notification::make()
                        ->title('Diskon berhasil dihapus')
                        ->body("Kode diskon \"{$code}\" telah di-soft-delete.")
                        ->success()
                        ->send();
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
     | ROW ACTIONS — Users (HARD DELETE)
     |=========================================================*/

    public function editUserAction(): Action
    {
        return Action::make('editUser')
            ->label('Ubah')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->link()
            ->size('sm')
            ->closeModalByClickingAway(false)
            ->modalHeading('Ubah User')
            ->modalDescription('Edit data user di bawah ini.')
            ->form($this->userFormSchema(isCreate: false))
            ->fillForm(function (array $arguments): array {
                $user = $arguments['user'] ?? [];
                return [
                    'name'  => $user['name'] ?? null,
                    'email' => $user['email'] ?? null,
                    'phone' => $user['phone'] ?? null,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $user = $arguments['user'] ?? [];
                $id   = $user['id'] ?? null;
                $name = $user['name'] ?? 'User';

                if (!$id) {
                    Notification::make()
                        ->title('ID user tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                $result = $apiService->updateUser((string) $id, $this->normalizeUserPayload($data, isCreate: false));

                if ($result !== null) {
                    Notification::make()
                        ->title('User berhasil diperbarui')
                        ->body("User \"{$name}\" telah berhasil diperbarui.")
                        ->success()
                        ->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal memperbarui user')
                        ->body('Terjadi kesalahan saat menghubungi API.')
                        ->danger()
                        ->send();
                }
            });
    }

    public function deleteUserAction(): Action
    {
        return Action::make('deleteUser')
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->link()
            ->size('sm')
            ->closeModalByClickingAway(false)
            ->requiresConfirmation()
            ->modalHeading('Hapus User')
            ->modalDescription(function (array $arguments): string {
                $user = $arguments['user'] ?? [];
                $name = $user['name'] ?? 'User';
                return "Apakah Anda yakin ingin menghapus user \"{$name}\"? Masukkan password Anda untuk mengkonfirmasi penghapusan.";
            })
            ->modalSubmitActionLabel('Ya, Hapus')
            ->modalCancelActionLabel('Batal')
            ->form([
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!Hash::check($value, Auth::user()->password)) {
                                    $fail('Password yang Anda masukkan salah.');
                                }
                            };
                        },
                    ]),
            ])
            ->action(function (array $data, array $arguments): void {
                $user = $arguments['user'] ?? [];
                $id   = $user['id'] ?? null;
                $name = $user['name'] ?? 'User';

                if (!$id) {
                    Notification::make()
                        ->title('ID user tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $apiService = new SmartFormsApiService();
                // HARD DELETE untuk user
                $success = $apiService->deleteUser((string) $id);

                if ($success) {
                    Notification::make()
                        ->title('User berhasil dihapus')
                        ->body("User \"{$name}\" telah berhasil dihapus.")
                        ->success()
                        ->send();
                    $this->loadData();
                } else {
                    Notification::make()
                        ->title('Gagal menghapus user')
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

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->required()
                    ->helperText('Contoh: Rp 99.000'),

                Forms\Components\Toggle::make('perBulan')
                    ->label('Per Bulan?')
                    ->inline(false),
            ]),

            Forms\Components\Grid::make(2)->schema([
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

            Forms\Components\Grid::make(2)->schema([
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
        // Build options dari packages yang sudah di-load
        $packageOptions = [];
        foreach ($this->packages as $pkg) {
            $slug = $pkg['slug'] ?? null;
            $nama = $pkg['nama'] ?? null;
            if ($slug && $nama) {
                $packageOptions[$slug] = $nama;
            }
        }

        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Diskon')
                    ->required()
                    ->maxLength(50)
                    ->helperText('Contoh: WELCOME10, FLASH50, HEMAT20'),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Diskon')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Nama/deskripsi kode diskon untuk memudahkan identifikasi.'),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'general'     => 'Umum (semua paket)',
                        'per_package' => 'Per Paket',
                    ])
                    ->default('general')
                    ->required()
                    ->live()
                    ->helperText('Umum = berlaku untuk semua paket. Per Paket = hanya untuk paket tertentu.'),

                Forms\Components\TextInput::make('discount_percent')
                    ->label('Persen Diskon (%)')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(1)
                    ->maxValue(100)
                    ->required()
                    ->helperText('Nilai diskon dalam persen. Contoh: 10, 15.5, 25.'),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('min_purchase')
                    ->label('Minimal Belanja (Rp)')
                    ->numeric()
                    ->default(0)
                    ->helperText('Minimal harga transaksi agar diskon berlaku. Isi 0 untuk tanpa minimal.'),

                Forms\Components\TextInput::make('max_discount')
                    ->label('Maksimal Potongan (Rp)')
                    ->numeric()
                    ->default(0)
                    ->helperText('Maksimum potongan yang bisa didapat. Isi 0 untuk tanpa batas.'),
            ]),

            Forms\Components\Select::make('applicable_packages')
                ->label('Paket Berlaku')
                ->options($packageOptions)
                ->multiple()
                ->searchable()
                ->visible(fn(Forms\Get $get) => $get('type') === 'per_package')
                ->columnSpanFull()
                ->helperText('Pilih satu atau lebih paket yang berlaku untuk diskon ini.')
                ->placeholder('Pilih paket...'),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('usage_limit')
                    ->label('Batas Total Penggunaan')
                    ->numeric()
                    ->default(0)
                    ->helperText('Total pemakaian diskon oleh semua user. Isi 0 untuk unlimited.'),

                Forms\Components\TextInput::make('usage_limit_per_user')
                    ->label('Batas per User')
                    ->numeric()
                    ->default(1)
                    ->helperText('Maksimum pemakaian diskon per user. Isi 0 untuk unlimited, 1 untuk sekali pakai.'),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Mulai Berlaku')
                    ->native(false)
                    ->helperText('Kosongkan jika langsung berlaku.')
                    ->dehydrateStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Berlaku Sampai')
                    ->native(false)
                    ->helperText('Kosongkan jika tidak ada batas waktu (unlimited).')
                    ->dehydrateStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d H:i:s') : null),
            ]),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->columnSpanFull()
                ->helperText('Nonaktifkan untuk menonaktifkan diskon sementara tanpa menghapus data.'),
        ];
    }

    protected function userFormSchema(bool $isCreate = true): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]),

            Forms\Components\TextInput::make('phone')
                ->label('No. Telepon')
                ->tel()
                ->maxLength(20)
                ->helperText('Contoh: 08123456789')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->required()
                ->minLength(8)
                ->maxLength(255)
                ->helperText('Minimal 8 karakter.')
                ->suffixAction(
                    Forms\Components\Actions\Action::make('generatePassword')
                        ->icon('heroicon-o-arrow-path')
                        ->tooltip('Generate password acak')
                        ->action(function (Forms\Set $set): void {
                            $set('password', \Illuminate\Support\Str::random(12));
                        })
                )
                ->visible($isCreate)
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
            'discount_percent'     => (float) ($data['discount_percent'] ?? 0),
            'min_purchase'         => (int) ($data['min_purchase'] ?? 0),
            'max_discount'         => (int) ($data['max_discount'] ?? 0),
            'applicable_packages'  => $type === 'per_package' ? array_values($data['applicable_packages'] ?? []) : null,
            'usage_limit'          => (int) ($data['usage_limit'] ?? 0),
            'usage_limit_per_user' => (int) ($data['usage_limit_per_user'] ?? 1),
            'is_active'            => (bool) ($data['is_active'] ?? true),
            'starts_at'            => $data['starts_at'] ?? null,
            'expires_at'           => $data['expires_at'] ?? null,
        ];
    }

    private function normalizeUserPayload(array $data, bool $isCreate = true): array
    {
        $payload = [
            'name'  => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ];

        if ($isCreate) {
            $payload['password'] = $data['password'] ?? null;
        }

        return $payload;
    }
}
