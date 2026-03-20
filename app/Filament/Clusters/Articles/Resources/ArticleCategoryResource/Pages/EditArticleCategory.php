<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleCategoryResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleCategory extends EditRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ArticleCategoryResource::getUrl('index', ['activeTab' => 'categories']);
    }
}
