<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleResource;
use App\Filament\Clusters\Articles\Resources\ArticleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    public function getTabs(): array
    {
        return [
            'articles' => Tab::make('Articles')
                ->icon('heroicon-m-document-text')
                ->badge(ArticleResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ArticleCategoryResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'categories') {
            $this->redirect('/management/articles/article-categories?activeTab=categories');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
