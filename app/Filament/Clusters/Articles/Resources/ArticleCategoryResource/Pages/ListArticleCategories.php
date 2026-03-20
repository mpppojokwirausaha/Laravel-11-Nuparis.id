<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleCategoryResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleCategoryResource;
use App\Filament\Clusters\Articles\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArticleCategories extends ListRecords
{
    protected static string $resource = ArticleCategoryResource::class;

    public function getTabs(): array
    {
        return [
            'articles' => Tab::make('Articles')
                ->icon('heroicon-m-document-text')
                ->badge(ArticleResource::getModel()::count()),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ArticleCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'articles') {
            $this->redirect('/management/articles/articles');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
