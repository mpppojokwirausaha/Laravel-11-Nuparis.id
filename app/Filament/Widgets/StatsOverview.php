<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Review;
use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $ActivityStat = Activity::getStat();
        $ArticleStat = Article::getStat();
        $EventStat = Event::getStat();
        $PartnerStat = Partner::getStat();
        $NewsStat = News::getStat();
        $ReviewStat = Review::getStat();

        return [
            Stat::make('Aktivitas bulan ini', $ActivityStat['currentCount'] . ' Aktivitas')
                ->description($ActivityStat['description'])
                ->descriptionIcon($ActivityStat['icon'])
                ->chart($ActivityStat['chart'])
                ->color($ActivityStat['color'])
                ->url(route('filament.management.activities.resources.activities.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),

            Stat::make('Artikel bulan ini', $ArticleStat['currentCount'] . ' Artikel')
                ->description($ArticleStat['description'])
                ->descriptionIcon($ArticleStat['icon'])
                ->chart($ArticleStat['chart'])
                ->color($ArticleStat['color'])
                ->url(route('filament.management.articles.resources.articles.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),

            Stat::make('Event bulan ini', $EventStat['currentCount'] . ' Event')
                ->description($EventStat['description'])
                ->descriptionIcon($EventStat['icon'])
                ->chart($EventStat['chart'])
                ->color($EventStat['color'])
                ->url(route('filament.management.events.resources.events.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),

            Stat::make('Mitra bulan ini', $PartnerStat['currentCount'] . ' Mitra')
                ->description($PartnerStat['description'])
                ->descriptionIcon($PartnerStat['icon'])
                ->chart($PartnerStat['chart'])
                ->color($PartnerStat['color'])
                ->url(route('filament.management.resources.partners.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),

            Stat::make('Berita bulan ini', $NewsStat['currentCount'] . ' Berita')
                ->description($NewsStat['description'])
                ->descriptionIcon($NewsStat['icon'])
                ->chart($NewsStat['chart'])
                ->color($NewsStat['color'])
                ->url(route('filament.management.resources.news.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),

            Stat::make('Ulasan bulan ini', $ReviewStat['currentCount'] . ' Ulasan')
                ->description($ReviewStat['description'])
                ->descriptionIcon($ReviewStat['icon'])
                ->chart($ReviewStat['chart'])
                ->color($ReviewStat['color'])
                ->url(route('filament.management.resources.reviews.index'))
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-all duration-300',
                    'wire:navigate' => true,
                ]),
        ];
    }
}
