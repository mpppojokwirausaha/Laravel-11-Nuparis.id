<?php

namespace App\Filament\Clusters\Articles\Resources;

use App\Filament\Clusters\Articles;
use App\Filament\Clusters\Articles\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Articles::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    Group::make()
                        ->schema([
                            TextInput::make('article_title')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('article_slug', Str::slug($state)))
                                ->required(),
                            TextInput::make('article_slug')
                                ->placeholder('Auto Generated'),
                        ])->columns(2),
                    RichEditor::make('article_description')
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('img_articles')
                        ->fileAttachmentsVisibility('public')
                        ->required()
                        ->columnSpanFull(),
                    Group::make()
                        ->schema([
                            Select::make('article_category_uuid')
                                ->label('Article Category')
                                ->relationship(name: 'articleCategory', titleAttribute: 'article_category_name')
                                ->createOptionForm([
                                    TextInput::make('article_category_name')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('article_category_slug', Str::slug($state)))
                                        ->required(),
                                    TextInput::make('article_category_slug')
                                        ->required()
                                ])->required(),
                            // ->searchable(),
                            FileUpload::make('article_image')
                                ->image()
                                ->disk('public')->directory('img_articles')
                                ->required()
                                ->downloadable()
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ]),
                        ])->columns(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('article_image')
                    ->label('IMAGE')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('article_title')
                    ->label('TITLE')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('articleCategory.article_category_name')
                    ->searchable()
                    ->sortable()
                    ->label('CATEGORY'),
                TextColumn::make('created_at')
                    ->label('LOG')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->emptyStateActions([
                Action::make('create')
                    ->label('Create Article')
                    ->url(route('filament.management.articles.resources.articles.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
