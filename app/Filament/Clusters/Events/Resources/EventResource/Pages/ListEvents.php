<?php

namespace App\Filament\Clusters\Events\Resources\EventResource\Pages;

use App\Filament\Clusters\Events\Resources\EventCategoryResource;
use App\Filament\Clusters\Events\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    public function getTabs(): array
    {
        return [
            'events' => Tab::make('Events')
                ->icon('heroicon-m-document-text')
                ->badge(EventResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(EventCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'categories') {
            $this->redirect('/management/events/event-categories?activeTab=categories');
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
