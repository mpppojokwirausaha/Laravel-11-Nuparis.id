<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleCategoryResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleCategory extends CreateRecord
{
    protected static string $resource = ArticleCategoryResource::class;
}
