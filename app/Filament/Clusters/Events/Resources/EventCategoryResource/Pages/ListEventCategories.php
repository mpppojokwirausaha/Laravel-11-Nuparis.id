<?php

namespace App\Filament\Clusters\Events\Resources\EventCategoryResource\Pages;

use App\Filament\Clusters\Events\Resources\EventCategoryResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEventCategories extends ListRecords
{
    protected static string $resource = EventCategoryResource::class;

    public function getTabs(): array
    {
        return [
            'events' => Tab::make('Events')
                ->icon('heroicon-m-document-text')
                ->badge(Event::getModel()::count()),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(EventCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'events') {
            $this->redirect('/management/events/events');
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
