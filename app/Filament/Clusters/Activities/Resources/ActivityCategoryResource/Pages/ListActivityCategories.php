<?php

namespace App\Filament\Clusters\Activities\Resources\ActivityCategoryResource\Pages;

use App\Filament\Clusters\Activities\Resources\ActivityCategoryResource;
use App\Filament\Clusters\Activities\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListActivityCategories extends ListRecords
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'activities' => Tab::make('Activities')
                ->icon('heroicon-m-document-text')
                ->badge(ActivityResource::getModel()::count()),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ActivityCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    // PERBAIKI: HAPUS PARAMETER
    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'activities') {
            $this->redirect('/management/activities/activities');
        }
    }
}
