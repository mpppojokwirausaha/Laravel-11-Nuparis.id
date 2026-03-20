<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityCategoryResource;
use App\Filament\Clusters\Activities\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    public function getTabs(): array
    {
        return [
            'articles' => Tab::make('Activities')
                ->icon('heroicon-m-document-text')
                ->badge(ActivityResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ActivityCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'categories') {
            $this->redirect('/management/activities/activity-categories?activeTab=categories');
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
