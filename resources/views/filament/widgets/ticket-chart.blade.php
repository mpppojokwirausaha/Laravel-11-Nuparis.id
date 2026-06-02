<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-x-2">
                    <x-heroicon-o-ticket class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                    <span>Dashboard Tiket</span>
                </div>
                <select wire:model.change="selectedPeriod"
                    class="appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-300 py-1.5 pl-3 pr-6 cursor-pointer outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="hari_ini">Hari ini</option>
                    <option value="bulan_ini">Bulan ini</option>
                    <option value="tahun_ini">Tahun ini</option>
                </select>
            </div>
        </x-slot>

        <x-slot name="description">
            <div class="flex items-center gap-2">
                <span>Ringkasan semua status tiket</span>
                <span class="text-xs text-gray-400"></span>
            </div>
        </x-slot>

        @php
            $colorMap = [
                'blue' => [
                    'key' => 'primary',
                    'bg' => 'from-blue-500 to-blue-600',
                    'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                    'dot' => 'bg-blue-500',
                    'bar' => 'bg-blue-500',
                ],
                'amber' => [
                    'key' => 'warning',
                    'bg' => 'from-amber-500 to-amber-600',
                    'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                    'dot' => 'bg-amber-500',
                    'bar' => 'bg-amber-500',
                ],
                'teal' => [
                    'key' => 'info',
                    'bg' => 'from-teal-500 to-teal-600',
                    'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
                    'dot' => 'bg-teal-500',
                    'bar' => 'bg-teal-500',
                ],
                'green' => [
                    'key' => 'success',
                    'bg' => 'from-green-500 to-green-600',
                    'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                    'dot' => 'bg-green-500',
                    'bar' => 'bg-green-500',
                ],
            ];

            $iconMap = [
                'blue' => 'TK',
                'amber' => 'PD',
                'teal' => 'OP',
                'green' => 'CL',
            ];

            $totalAll = collect($columns)->sum(fn($c) => $c['counts'][$selectedPeriod] ?? 0);

            // PERBAIKAN: Label perbandingan yang benar
            $comparisonLabels = [
                'hari_ini' => 'vs kemarin',
                'bulan_ini' => 'vs bulan lalu',
                'tahun_ini' => 'vs tahun lalu',
            ];
            $comparisonLabel = $comparisonLabels[$selectedPeriod] ?? 'vs periode sebelumnya';

            // Fungsi helper untuk hitung trend
            function calculateTrend($current, $previous)
            {
                if ($previous === null || $previous == 0) {
                    return $current > 0 ? 100 : null;
                }
                return round((($current - $previous) / $previous) * 100);
            }
        @endphp

        <style>
            /* Modern Grid Layout */
            .tk-modern-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1px;
                background: rgb(var(--gray-200));
                border-radius: 12px;
                overflow: hidden;
                margin-top: 8px;
            }

            .dark .tk-modern-grid {
                background: rgba(255, 255, 255, 0.1);
            }

            /* Card Style */
            .tk-modern-card {
                background: rgb(var(--gray-50));
                padding: 20px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .dark .tk-modern-card {
                background: rgb(var(--gray-800));
            }

            .tk-modern-card:hover {
                transform: translateY(-2px);
                background: white;
                box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .dark .tk-modern-card:hover {
                background: rgb(var(--gray-750));
                box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.3);
            }

            /* Header with icon */
            .tk-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .tk-icon-large {
                font-size: 14px;
                font-weight: 700;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                background: rgba(0, 0, 0, 0.04);
                letter-spacing: 0.5px;
            }

            .dark .tk-icon-large {
                background: rgba(255, 255, 255, 0.05);
            }

            /* Main Number */
            .tk-main-number {
                font-size: 36px;
                font-weight: 800;
                line-height: 1;
                margin-bottom: 8px;
                letter-spacing: -0.02em;
            }

            /* Label styling */
            .tk-label-modern {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: rgb(var(--gray-400));
            }

            /* Stats row */
            .tk-stats-row {
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid rgb(var(--gray-200));
            }

            .dark .tk-stats-row {
                border-top-color: rgba(255, 255, 255, 0.08);
            }

            /* Trend indicator */
            .tk-trend-modern {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 20px;
            }

            .tk-trend-up {
                background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
                color: #22c55e;
            }

            .tk-trend-down {
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
                color: #ef4444;
            }

            .tk-trend-flat {
                background: rgba(100, 116, 139, 0.1);
                color: #64748b;
            }

            /* Period list */
            .tk-period-list {
                margin-top: 16px;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .tk-period-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 6px 8px;
                border-radius: 8px;
                transition: all 0.2s;
                text-decoration: none;
                cursor: pointer;
            }

            .tk-period-item:hover {
                background: rgba(0, 0, 0, 0.04);
                transform: translateX(4px);
            }

            .dark .tk-period-item:hover {
                background: rgba(255, 255, 255, 0.05);
            }

            .tk-period-item.tk-active {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(59, 130, 246, 0.03));
                border-left: 3px solid rgb(59, 130, 246);
            }

            .tk-period-label {
                font-size: 12px;
                font-weight: 500;
                color: rgb(var(--gray-600));
            }

            .dark .tk-period-label {
                color: rgb(var(--gray-400));
            }

            .tk-period-value {
                font-size: 14px;
                font-weight: 700;
            }

            /* Sparkline bars */
            .tk-sparkline {
                display: flex;
                align-items: flex-end;
                gap: 2px;
                height: 20px;
            }

            .tk-spark-bar {
                width: 4px;
                border-radius: 2px;
                transition: height 0.3s ease;
            }

            /* Animation */
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .tk-modern-card {
                animation: slideInUp 0.4s ease-out both;
            }

            .tk-modern-card:nth-child(1) {
                animation-delay: 0.05s;
            }

            .tk-modern-card:nth-child(2) {
                animation-delay: 0.1s;
            }

            .tk-modern-card:nth-child(3) {
                animation-delay: 0.15s;
            }

            .tk-modern-card:nth-child(4) {
                animation-delay: 0.2s;
            }

            /* Active indicator dot */
            .tk-active-dot {
                display: inline-block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: rgb(59, 130, 246);
                margin-left: 6px;
            }

            /* Card click wrapper */
            .tk-card-link {
                cursor: pointer;
            }
        </style>

        <div class="tk-modern-grid">
            @foreach ($columns as $col)
                @php
                    $current = $col['counts'][$selectedPeriod] ?? 0;
                    $pct = $totalAll > 0 ? round(($current / $totalAll) * 100) : 0;

                    // PERBAIKAN: Hitung trend dengan prev_counts yang sudah dikirim dari widget
                    $previous = $col['prev_counts'][$selectedPeriod] ?? null;
                    $trendPct = calculateTrend($current, $previous);

                    $sparkValues = array_values($col['counts']);
                    $sparkMax = max($sparkValues) ?: 1;
                    $sparkBars = array_map(fn($v) => max(3, round(($v / $sparkMax) * 16)), $sparkValues);
                    $periodKeys = array_keys($periodLabels);

                    $colorHex =
                        $col['color'] === 'blue'
                            ? '59, 130, 246'
                            : ($col['color'] === 'amber'
                                ? '245, 158, 11'
                                : ($col['color'] === 'teal'
                                    ? '20, 184, 166'
                                    : '34, 197, 94'));

                    $statusUuid = $col['status'] ?? null;
                @endphp

                <!-- Card dengan wire:click -->
                <div class="tk-modern-card tk-card-link"
                    wire:click="goToTickets('{{ $statusUuid }}', '{{ $selectedPeriod }}')" style="cursor: pointer;">

                    <!-- Header -->
                    <div class="tk-card-header">
                        <div class="tk-icon-large" style="color: rgb({{ $colorHex }});">
                            {{ $iconMap[$col['color']] }}
                        </div>
                        <div class="tk-label-modern">{{ $col['label'] }}</div>
                    </div>

                    <!-- Main Number -->
                    <div class="tk-main-number" style="color: rgb({{ $colorHex }});">
                        {{ number_format($current) }}
                    </div>

                    <!-- Stats Row -->
                    <div class="tk-stats-row">
                        <div class="tk-label-modern">dari total {{ number_format($totalAll) }}</div>
                        <div class="tk-label-modern">{{ $pct }}%</div>
                    </div>

                    <!-- Progress Bar -->
                    <div
                        style="margin: 12px 0; height: 4px; background: rgba(0,0,0,0.05); border-radius: 99px; overflow: hidden;">
                        <div
                            style="width: {{ $pct }}%; height: 100%; background: rgb({{ $colorHex }}); border-radius: 99px; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);">
                        </div>
                    </div>

                    <!-- PERBAIKAN: Trend dengan label yang benar -->
                    @if ($trendPct !== null)
                        @php
                            $trendClass =
                                $trendPct > 0 ? 'tk-trend-up' : ($trendPct < 0 ? 'tk-trend-down' : 'tk-trend-flat');
                            $arrow = $trendPct > 0 ? '▲' : ($trendPct < 0 ? '▼' : '●');
                            $sign = $trendPct > 0 ? '+' : '';
                        @endphp
                        <div style="margin-bottom: 12px;">
                            <span class="tk-trend-modern {{ $trendClass }}">
                                {{ $arrow }} {{ $sign }}{{ $trendPct }}%
                                {{ $comparisonLabel }}
                            </span>
                        </div>
                    @endif

                    <!-- Period List -->
                    <div class="tk-period-list" wire:key="period-list-{{ $col['key'] }}">
                        @foreach ($periodLabels as $periodKey => $periodLabel)
                            @php
                                $isActive = $periodKey === $selectedPeriod;
                                $rowVal = $col['counts'][$periodKey] ?? 0;
                                $sparkIdx = array_search($periodKey, $periodKeys);
                            @endphp
                            <div wire:click.stop="goToTickets('{{ $statusUuid }}', '{{ $periodKey }}')"
                                class="tk-period-item {{ $isActive ? 'tk-active' : '' }}">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="tk-period-label">{{ $periodLabel }}</span>
                                    @if ($isActive)
                                        <span class="tk-active-dot"></span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <!-- Sparkline -->
                                    <div class="tk-sparkline">
                                        @foreach ($sparkBars as $si => $sh)
                                            <div class="tk-spark-bar"
                                                style="height: {{ $sh }}px; background: {{ $si === $sparkIdx ? 'rgb(' . $colorHex . ')' : 'rgba(' . $colorHex . ', 0.3)' }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <span class="tk-period-value" style="color: rgb({{ $colorHex }});">
                                        {{ number_format($rowVal) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Decorative gradient line at bottom -->
                    <div
                        style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, rgb({{ $colorHex }}) 0%, transparent 100%); opacity: 0.5; pointer-events: none;">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Footer note -->
        <div style="margin-top: 16px; text-align: center; font-size: 10px; color: rgb(var(--gray-400));">
            Klik card untuk melihat semua tiket dengan status tersebut | Klik periode untuk filter spesifik
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
