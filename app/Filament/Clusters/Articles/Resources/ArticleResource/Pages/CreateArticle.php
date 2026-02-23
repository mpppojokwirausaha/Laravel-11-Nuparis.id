<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
