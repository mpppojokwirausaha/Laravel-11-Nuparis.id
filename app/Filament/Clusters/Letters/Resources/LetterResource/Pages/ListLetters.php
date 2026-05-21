<?php

namespace App\Filament\Clusters\Letters\Resources\LetterResource\Pages;

use App\Filament\Clusters\Letters\Resources\LetterCategoryResource;
use App\Filament\Clusters\Letters\Resources\LetterResource;
use App\Filament\Clusters\Letters\Resources\OrderLetterResource;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\Order;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLetters extends ListRecords
{
    protected static string $resource = LetterResource::class;

    public function getTabs(): array
    {
        return [
            'letters' => Tab::make('Letters')
                ->icon('heroicon-m-document-text')
                ->badge(Letter::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(LetterCategory::count()),

            'orders' => Tab::make('Orders')
                ->icon('heroicon-m-shopping-cart')
                ->badge(Order::query()->where('order_reference_type', 'letter')->where('order_id', 'like', 'SCRIDB.NUPARIS.ID%')->count()),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'letters')    $this->redirect(LetterResource::getUrl('index'));
        if ($this->activeTab === 'categories') $this->redirect(LetterCategoryResource::getUrl('index') . '?activeTab=categories');
        if ($this->activeTab === 'orders')     $this->redirect(OrderLetterResource::getUrl('index') . '?activeTab=orders');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('visit_website')
                ->label('Visit ')
                ->icon('heroicon-o-globe-alt')
                ->url(env('DOMAIN_LETTERS'))
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }
}
