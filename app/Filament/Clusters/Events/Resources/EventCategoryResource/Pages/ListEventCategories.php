<?php

namespace App\Filament\Clusters\Events\Resources\EventCategoryResource\Pages;

use App\Filament\Clusters\Events\Resources\EventCategoryResource;
use App\Filament\Clusters\Events\Resources\EventResource;
use App\Filament\Clusters\Events\Resources\OrderEventResource;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
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
                ->badge(Event::count()),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(EventCategory::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'orders' => Tab::make('Orders')
                ->icon('heroicon-m-currency-dollar')
                ->badge(Order::query()
                    ->where('order_reference_type', 'event')
                    ->where('order_id', 'like', 'NUPARIS.ID-EVENT-%')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'events')     $this->redirect(EventResource::getUrl('index'));
        if ($this->activeTab === 'categories') $this->redirect(EventCategoryResource::getUrl('index') . '?activeTab=categories');
        if ($this->activeTab === 'orders')     $this->redirect(OrderEventResource::getUrl('index') . '?activeTab=orders');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
