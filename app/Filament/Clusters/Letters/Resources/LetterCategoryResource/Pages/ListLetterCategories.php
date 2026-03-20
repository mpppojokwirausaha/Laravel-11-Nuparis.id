<?php

namespace App\Filament\Clusters\Letters\Resources\LetterCategoryResource\Pages;

use App\Filament\Clusters\Letters\Resources\LetterCategoryResource;
use App\Filament\Clusters\Letters\Resources\OrderLetterResource;
use App\Http\Resources\LetterResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListLetterCategories extends ListRecords
{
    protected static string $resource = LetterCategoryResource::class;


    public function getTabs(): array
    {
        return [
            'letter' => Tab::make('Letter')
                ->icon('heroicon-m-document-text')
                ->badge(LetterResource::getModel()::count()),

            'category' => Tab::make('Letter Category')
                ->icon('heroicon-m-tag')
                ->badge(LetterCategoryResource::getModel()::count()),

            'order' => Tab::make('Letter Order')
                ->icon('heroicon-m-shopping-cart')
                ->badge(OrderLetterResource::getModel()::count()),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'letter') {
            $this->redirect(LetterResource::getUrl('index'));
        }
        if ($this->activeTab === 'order') {
            $this->redirect(OrderLetterResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
