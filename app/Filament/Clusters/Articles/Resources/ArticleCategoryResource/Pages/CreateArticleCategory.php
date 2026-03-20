<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleCategoryResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return ArticleCategoryResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
