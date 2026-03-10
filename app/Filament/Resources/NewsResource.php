<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('news_title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('news_source')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('example: community whatsapp pojok wirausaha, jabar.tribunnews.com, etc'),
                        TextInput::make('news_url')
                            ->live(onBlur: true)
                            ->prefix('https://')
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('news_slug', Str::slug($state)))
                            ->required(),
                        TextInput::make('news_slug')
                            ->required()
                            ->placeholder('Auto Generated'),
                        RichEditor::make('news_content')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'link',
                                'orderedList',
                                'underline',
                                'undo',
                            ])->required()
                            ->columnSpanFull(),
                        FileUpload::make('news_image')
                            ->image()
                            ->disk('public')->directory('img_news')
                            ->required()
                            ->downloadable()
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                        FileUpload::make('news_avatar')
                            ->image()
                            ->disk('public')->directory('img_news')
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

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('news_image')
                    ->label('IMAGE'),
                TextColumn::make('news_source')
                    ->label('SOURCE')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('news_url')
                    ->searchable()
                    ->label('URL'),
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
                    ->label('Create Sosial Media')
                    ->url(route('filament.management.resources.news.create'))
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
