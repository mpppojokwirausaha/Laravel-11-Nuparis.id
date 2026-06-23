<x-filament-panels::page>

    {{-- STATS CARDS --}}
    <div class="flex gap-4 overflow-x-auto pb-2">

        <div class="flex-1 min-w-0">
            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-100 rounded-full dark:bg-primary-900/20">
                        <x-heroicon-o-document-text class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-white">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getTotal() }}</p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <div class="flex-1 min-w-0">
            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-success-100 rounded-full dark:bg-success-900/20">
                        <x-heroicon-o-currency-dollar class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-white">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <div class="flex-1 min-w-0">
            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-success-100 rounded-full dark:bg-success-900/20">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-white">Sukses</p>
                        <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                            {{ $this->getSuccessCount() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <div class="flex-1 min-w-0">
            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-warning-100 rounded-full dark:bg-warning-900/20">
                        <x-heroicon-o-clock class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-white">Pending</p>
                        <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                            {{ $this->getPendingCount() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>
        </div>

    </div>

    {{-- TABLE TRANSAKSI --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Data Transaksi</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Order ID</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            User</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Total</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Diskon</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Status</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getTransactions() as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">

                            {{-- Order ID --}}
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $tx['order_id'] ?? '-' }}
                            </td>

                            {{-- User --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $tx['user']['name'] ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-white">
                                    {{ $tx['user']['email'] ?? '' }}
                                </div>
                            </td>

                            {{-- Paket --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-filament::badge color="info">
                                    {{ $tx['package']['name'] ?? '-' }}
                                </x-filament::badge>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                Rp
                                {{ number_format((float) preg_replace('/[^0-9]/', '', $tx['amount'] ?? '0'), 0, ',', '.') }}
                            </td>

                            {{-- Diskon --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (!empty($tx['discount']['code']))
                                    <x-filament::badge color="success">
                                        {{ $tx['discount']['code'] }}
                                    </x-filament::badge>
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $status = $tx['status'] ?? '';
                                    $color = match ($status) {
                                        'success' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'cancelled' => 'gray',
                                        'expired' => 'gray',
                                        default => 'gray',
                                    };
                                    $label = match ($status) {
                                        'success' => 'Sukses',
                                        'pending' => 'Menunggu',
                                        'failed' => 'Gagal',
                                        'cancelled' => 'Dibatalkan',
                                        'expired' => 'Kadaluarsa',
                                        default => $status ?: '-',
                                    };
                                @endphp
                                <x-filament::badge :color="$color">
                                    {{ $label }}
                                </x-filament::badge>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                {{ $tx['created_at']
                                    ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $tx['created_at'])->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-white">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-inbox class="w-10 h-10 opacity-40" />
                                    <span>Tidak ada data transaksi.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- TABLE PACKAGES --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Daftar Paket</x-slot>
        <x-slot name="headerEnd">
            {{ $this->createPackageAction() }}
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Fitur</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Harga</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Max Form</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Max Submission</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Label</th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getPackages() as $pkg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">

                            {{-- Nama --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $pkg['nama'] ?? '-' }}
                            </td>

                            {{-- Fitur --}}
                            <td class="px-4 py-3">
                                <ul class="space-y-1">
                                    @foreach ($pkg['fitur'] ?? [] as $fitur)
                                        <li class="flex items-center gap-1 text-xs text-gray-600 dark:text-white">
                                            <x-heroicon-o-check class="w-3 h-3 text-success-500 flex-shrink-0" />
                                            {{ $fitur }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Harga --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $pkg['harga'] ?? '-' }}
                                @if ($pkg['perBulan'] ?? false)
                                    <span class="text-xs text-gray-400">/bln</span>
                                @endif
                            </td>

                            {{-- Max Form --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (($pkg['max_forms'] ?? 0) === -1)
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @else
                                    <span class="text-gray-900 dark:text-white">{{ $pkg['max_forms'] ?? '-' }}</span>
                                @endif
                            </td>

                            {{-- Max Submission --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (($pkg['max_submissions'] ?? 0) === -1)
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @else
                                    <span class="text-gray-900 dark:text-white">
                                        {{ number_format($pkg['max_submissions'] ?? 0, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Label --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex flex-col items-start gap-1">
                                    @if ($pkg['populer'] ?? false)
                                        <x-filament::badge color="warning"
                                            class="whitespace-nowrap">Populer</x-filament::badge>
                                    @endif
                                    @if ($pkg['pro'] ?? false)
                                        <x-filament::badge color="primary"
                                            class="whitespace-nowrap">Pro</x-filament::badge>
                                    @endif
                                    @if (!($pkg['populer'] ?? false) && !($pkg['pro'] ?? false))
                                        <span class="text-gray-400 dark:text-white text-xs">-</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-3">
                                    {{ $this->editPackageAction()(['package' => $pkg]) }}
                                    {{ $this->deletePackageAction()(['package' => $pkg]) }}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-white">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-inbox class="w-10 h-10 opacity-40" />
                                    <span>Tidak ada data paket.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- TABLE DISKON --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Daftar Diskon</x-slot>
        <x-slot name="description">
            {{ $this->getActiveDiscountCount() }} aktif dari {{ $this->getTotalDiscounts() }} total kode diskon
        </x-slot>
        <x-slot name="headerEnd">
            {{ $this->createDiscountAction() }}
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Kode</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Nama</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Tipe</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Diskon</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Min. Belanja</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Maks. Diskon</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket Berlaku</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Penggunaan</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Limit/User</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Mulai Berlaku</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Berlaku Sampai</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Status</th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getDiscounts() as $disc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">

                            {{-- Kode --}}
                            <td
                                class="px-4 py-3 font-mono text-xs font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['code'] ?? '-' }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['name'] ?? '-' }}
                            </td>

                            {{-- Tipe --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $type = $disc['type'] ?? '';
                                    $typeLabel = match ($type) {
                                        'general' => 'Umum',
                                        'per_package' => 'Per Paket',
                                        default => $type ?: '-',
                                    };
                                @endphp
                                <x-filament::badge :color="$type === 'per_package' ? 'info' : 'gray'">
                                    {{ $typeLabel }}
                                </x-filament::badge>
                            </td>

                            {{-- Diskon --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['discount_percent'] ?? 0 }}%
                            </td>

                            {{-- Min Belanja --}}
                            <td class="px-4 py-3 text-gray-500 dark:text-white whitespace-nowrap">
                                @if (($disc['min_purchase'] ?? 0) > 0)
                                    Rp {{ number_format($disc['min_purchase'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>

                            {{-- Maks Diskon --}}
                            <td class="px-4 py-3 text-gray-500 dark:text-white whitespace-nowrap">
                                @if (($disc['max_discount'] ?? 0) > 0)
                                    Rp {{ number_format($disc['max_discount'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>

                            {{-- Paket Berlaku --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (!empty($disc['applicable_packages']))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($disc['applicable_packages'] as $pkgSlug)
                                            <x-filament::badge color="primary">
                                                {{ \Illuminate\Support\Str::title($pkgSlug) }}
                                            </x-filament::badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-white">Semua Paket</span>
                                @endif
                            </td>

                            {{-- Penggunaan --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['usage_count'] ?? 0 }}
                                /
                                @if (($disc['usage_limit'] ?? 0) > 0)
                                    {{ $disc['usage_limit'] }}
                                @else
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @endif
                            </td>

                            {{-- Limit per User --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                @if (($disc['usage_limit_per_user'] ?? 0) > 0)
                                    {{ $disc['usage_limit_per_user'] }}x
                                @else
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @endif
                            </td>

                            {{-- Mulai Berlaku --}}
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                @if (!empty($disc['starts_at']))
                                    {{ \Carbon\Carbon::parse($disc['starts_at'])->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">Langsung aktif</span>
                                @endif
                            </td>

                            {{-- Berlaku Sampai --}}
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                @if (!empty($disc['expires_at']))
                                    {{ \Carbon\Carbon::parse($disc['expires_at'])->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">Tidak ada batas</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $isActive = $disc['is_active'] ?? false;
                                    $isExpired =
                                        !empty($disc['expires_at']) &&
                                        \Carbon\Carbon::parse($disc['expires_at'])->isPast();
                                @endphp
                                @if ($isExpired)
                                    <x-filament::badge color="gray">Kadaluarsa</x-filament::badge>
                                @elseif ($isActive)
                                    <x-filament::badge color="success">Aktif</x-filament::badge>
                                @else
                                    <x-filament::badge color="danger">Nonaktif</x-filament::badge>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-3">
                                    {{ $this->editDiscountAction()(['discount' => $disc]) }}
                                    {{ $this->deleteDiscountAction()(['discount' => $disc]) }}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-12 text-center text-gray-400 dark:text-white">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-inbox class="w-10 h-10 opacity-40" />
                                    <span>Tidak ada data diskon.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />

</x-filament-panels::page>
