<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('review_fullname')
                                    ->required(),
                                TextInput::make('review_link')
                                    ->live(onBlur: true)
                                    ->url()
                                    ->prefix('https://')
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('review_slug', Str::slug($state)))
                                    ->required(),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('review_rating')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('review_slug')
                                    ->required()
                                    ->placeholder('Auto Generated'),
                            ])->columns(2),
                        RichEditor::make('review_content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'link',
                                'orderedList',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                        FileUpload::make('review_avatar')
                            ->image()
                            ->disk('public')->directory('img_review')
                            ->required()
                            ->downloadable()
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                $name = 'review_' . $get('review_fullname') ?: 'review';

                                $slug = Str::of($name)
                                    ->lower()
                                    ->replace(' ', '_')
                                    ->slug('_')
                                    ->limit(50);

                                return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('review_avatar')
                    ->label('AVATAR')
                    ->circular()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('review_fullname')
                    ->label('FULLNAME')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('review_rating')
                    ->label('RATING')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('review_link')
                    ->label('LINK')
                    ->url(fn($record) => $record->review_link)
                    ->openUrlInNewTab()
                    ->limit('30')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('LOG')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
                    ->label('Create Review')
                    ->url(route('filament.management.resources.reviews.create'))
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
