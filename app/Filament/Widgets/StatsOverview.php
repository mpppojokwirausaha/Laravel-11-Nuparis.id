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
            Stat::make('Activities this month', $ActivityStat['currentCount'] . ' Activities')
                ->description($ActivityStat['description'])
                ->descriptionIcon($ActivityStat['icon'])
                ->chart($ActivityStat['chart'])
                ->color($ActivityStat['color']),

            Stat::make('Articles this month', $ArticleStat['currentCount'] . ' Articles')
                ->description($ArticleStat['description'])
                ->descriptionIcon($ArticleStat['icon'])
                ->chart($ArticleStat['chart'])
                ->color($ArticleStat['color']),

            Stat::make('Events this month', $EventStat['currentCount'] . ' Events')
                ->description($EventStat['description'])
                ->descriptionIcon($EventStat['icon'])
                ->chart($EventStat['chart'])
                ->color($EventStat['color']),

            Stat::make('Partners this month', $PartnerStat['currentCount'] . ' Partners')
                ->description($PartnerStat['description'])
                ->descriptionIcon($PartnerStat['icon'])
                ->chart($PartnerStat['chart'])
                ->color($PartnerStat['color']),

            Stat::make('News this month', $NewsStat['currentCount'] . ' News')
                ->description($NewsStat['description'])
                ->descriptionIcon($NewsStat['icon'])
                ->chart($NewsStat['chart'])
                ->color($NewsStat['color']),

            Stat::make('Reviews this month', $ReviewStat['currentCount'] . ' Reviews')
                ->description($ReviewStat['description'])
                ->descriptionIcon($ReviewStat['icon'])
                ->chart($ReviewStat['chart'])
                ->color($ReviewStat['color']),
        ];
    }
}
