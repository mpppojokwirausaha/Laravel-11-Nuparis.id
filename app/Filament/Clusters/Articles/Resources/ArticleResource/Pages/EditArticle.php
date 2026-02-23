<?php

namespace App\Filament\Clusters\Articles\Resources\ArticleResource\Pages;

use App\Filament\Clusters\Articles\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
