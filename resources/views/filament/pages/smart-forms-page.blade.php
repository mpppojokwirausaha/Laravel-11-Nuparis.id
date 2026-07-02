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
                            {{ $this->getSuccessCount() }}</p>
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
                            {{ $this->getPendingCount() }}</p>
                    </div>
                </div>
            </x-filament::card>
        </div>

    </div>

    {{-- TABLE TRANSAKSI --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Data Transaksi</x-slot>

        <div class="flex justify-end mb-4">
            <div class="w-48">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input type="text" wire:model.live.debounce.300ms="searchTransaction"
                        placeholder="Cari" />
                </x-filament::input.wrapper>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Order ID
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            User
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Total
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Diskon
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Tanggal
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getTransactions() as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $tx['order_id'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $tx['user']['name'] ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-white">{{ $tx['user']['email'] ?? '' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                    {{ $tx['package']['name'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                Rp
                                {{ number_format((float) preg_replace('/[^0-9]/', '', $tx['amount'] ?? '0'), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (!empty($tx['discount']['code']))
                                    <span
                                        class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                        {{ $tx['discount']['code'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $status = $tx['status'] ?? '';
                                    $badgeStyle = match ($status) {
                                        'success'
                                            => 'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30',
                                        'pending'
                                            => 'bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30',
                                        'failed'
                                            => 'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30',
                                        'cancelled'
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
                                        'expired'
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
                                        default
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
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
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $badgeStyle }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                {{ $tx['created_at'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $tx['created_at'])->format('d/m/Y H:i') : '-' }}
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

    {{-- TABLE USERS --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Daftar User</x-slot>
        <x-slot name="description">
            {{ $this->getActiveUserCount() }} aktif dari {{ $this->getTotalUsers() }} total user
        </x-slot>
        {{-- HAPUS TOMBOL TAMBAH USER --}}
        {{-- <x-slot name="headerEnd">
            {{ $this->createUserAction() }}
        </x-slot> --}}

        <div class="flex justify-end mb-4">
            <div class="w-48">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input type="text" wire:model.live.debounce.300ms="searchUser" placeholder="Cari" />
                </x-filament::input.wrapper>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Nama
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Email
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Telepon
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Terdaftar
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Status Akun
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Status Sub.
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aktif Sejak
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Expired
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getUsers() as $user)
                        @php
                            $sub = $user['subscription'] ?? [];
                            $subStatus = $sub['status'] ?? 'free';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $user['name'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ $user['email'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-white">
                                {{ $user['phone'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                {{ !empty($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (!empty($user['deleted_at']))
                                    <span
                                        class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-50 text-danger-700 ring-1 ring-inset ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                        Dihapus
                                    </span>
                                @elseif (!empty($user['email_verified_at']))
                                    <span
                                        class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-50 text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30">
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $packageBadgeStyle =
                                        $subStatus === 'active'
                                            ? 'bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30'
                                            : 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30';
                                @endphp
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $packageBadgeStyle }}">
                                    {{ $sub['package_name'] ?? 'Gratis' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $subBadgeStyle = match ($subStatus) {
                                        'active'
                                            => 'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30',
                                        'pending'
                                            => 'bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30',
                                        'expired'
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
                                        'cancelled'
                                            => 'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30',
                                        default
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $subBadgeStyle }}">
                                    {{ $sub['status_label'] ?? 'Gratis' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                {{ !empty($sub['starts_at']) ? \Carbon\Carbon::parse($sub['starts_at'])->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                @if (!empty($sub['expires_at']))
                                    @php $exp = \Carbon\Carbon::parse($sub['expires_at']); @endphp
                                    <span
                                        class="{{ $exp->isPast() ? 'text-danger-500' : 'text-gray-500 dark:text-white' }}">
                                        {{ $exp->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-3">
                                    {{ $this->editUserAction()(['user' => $user]) }}
                                    {{ $this->deleteUserAction()(['user' => $user]) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400 dark:text-white">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-inbox class="w-10 h-10 opacity-40" />
                                    <span>Tidak ada data user.</span>
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

        <div class="flex justify-end mb-4">
            <div class="w-48">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input type="text" wire:model.live.debounce.300ms="searchPackage"
                        placeholder="Cari" />
                </x-filament::input.wrapper>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Paket
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Fitur
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Harga
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Max Form
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Max Submission
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Label
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getPackages() as $pkg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $pkg['nama'] ?? '-' }}
                            </td>
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
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $pkg['harga'] ?? '-' }}
                                @if ($pkg['perBulan'] ?? false)
                                    <span class="text-xs text-gray-400">/bln</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (($pkg['max_forms'] ?? 0) === -1)
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @else
                                    <span class="text-gray-900 dark:text-white">{{ $pkg['max_forms'] ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (($pkg['max_submissions'] ?? 0) === -1)
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @else
                                    <span
                                        class="text-gray-900 dark:text-white">{{ number_format($pkg['max_submissions'] ?? 0, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-start gap-1">
                                    @if ($pkg['populer'] ?? false)
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-50 text-warning-700 ring-1 ring-inset ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">
                                            Populer
                                        </span>
                                    @endif
                                    @if ($pkg['pro'] ?? false)
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                            Pro
                                        </span>
                                    @endif
                                    @if (!($pkg['populer'] ?? false) && !($pkg['pro'] ?? false))
                                        <span class="text-gray-400 dark:text-white text-xs">-</span>
                                    @endif
                                </div>
                            </td>
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
        @php
            // Ambil mapping slug → nama asli paket dari API
            $packageNameMap = $this->getPackageNameMap();
        @endphp

        <x-slot name="heading">Daftar Diskon</x-slot>
        <x-slot name="description">
            {{ $this->getActiveDiscountCount() }} aktif dari {{ $this->getTotalDiscounts() }} total kode diskon
        </x-slot>
        <x-slot name="headerEnd">
            {{ $this->createDiscountAction() }}
        </x-slot>

        <div class="flex justify-end mb-4">
            <div class="w-48">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input type="text" wire:model.live.debounce.300ms="searchDiscount"
                        placeholder="Cari" />
                </x-filament::input.wrapper>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Kode
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white">
                            Nama
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white">
                            Tipe
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Diskon
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Min. Belanja
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Maks. Diskon
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white">
                            Paket Berlaku
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Penggunaan
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Limit/User
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Mulai Berlaku
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Berlaku Sampai
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white">
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-white whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($this->getDiscounts() as $disc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td
                                class="px-4 py-3 font-mono text-xs font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['code'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ $disc['name'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $type = $disc['type'] ?? '';
                                    $typeLabel = match ($type) {
                                        'general' => 'Umum',
                                        'per_package' => 'Per Paket',
                                        default => $type ?: '-',
                                    };
                                    $typeBadgeStyle =
                                        $type === 'per_package'
                                            ? 'bg-info-50 text-info-700 ring-info-600/20 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30'
                                            : 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30';
                                @endphp
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $typeBadgeStyle }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['discount_percent'] ?? 0 }}%
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-white whitespace-nowrap">
                                @if (($disc['min_purchase'] ?? 0) > 0)
                                    Rp {{ number_format($disc['min_purchase'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-white whitespace-nowrap">
                                @if (($disc['max_discount'] ?? 0) > 0)
                                    Rp {{ number_format($disc['max_discount'], 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (!empty($disc['applicable_packages']))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($disc['applicable_packages'] as $pkgSlug)
                                            <span
                                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                {{ $packageNameMap[$pkgSlug] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $pkgSlug)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-white">Semua Paket</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $disc['usage_count'] ?? 0 }} /
                                @if (($disc['usage_limit'] ?? 0) > 0)
                                    {{ $disc['usage_limit'] }}
                                @else
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white whitespace-nowrap">
                                @if (($disc['usage_limit_per_user'] ?? 0) > 0)
                                    {{ $disc['usage_limit_per_user'] }}x
                                @else
                                    <span class="text-gray-400 dark:text-white">∞</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                @if (!empty($disc['starts_at']))
                                    {{ \Carbon\Carbon::parse($disc['starts_at'])->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">Langsung aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-white whitespace-nowrap">
                                @if (!empty($disc['expires_at']))
                                    {{ \Carbon\Carbon::parse($disc['expires_at'])->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 dark:text-white">Tidak ada batas</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $isActive = $disc['is_active'] ?? false;
                                    $isExpired =
                                        !empty($disc['expires_at']) &&
                                        \Carbon\Carbon::parse($disc['expires_at'])->isPast();
                                    $statusBadgeStyle = match (true) {
                                        $isExpired
                                            => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30',
                                        $isActive
                                            => 'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30',
                                        default
                                            => 'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30',
                                    };
                                    $statusLabel = match (true) {
                                        $isExpired => 'Kadaluarsa',
                                        $isActive => 'Aktif',
                                        default => 'Nonaktif',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusBadgeStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
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
