<?php

namespace App\Filament\Clusters\Activities\Resources;

use App\Filament\Clusters\Activities;
use App\Filament\Clusters\Activities\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;
    protected static ?string $cluster = Activities::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Cluster hilang dari sidebar
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('activity_title')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('activity_slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('activity_slug')
                                    ->label('Activity Slug')
                                    ->placeholder('Auto Generated')
                                    ->required(),
                            ])->columns(2),
                        RichEditor::make('activity_description')
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
                            ->fileAttachmentsDirectory('img_activities')
                            ->fileAttachmentsVisibility('public')
                            ->required()
                            ->columnSpanFull(),
                        Group::make()
                            ->schema([
                                Select::make('activity_category_uuid')
                                    ->label('Activity Category')
                                    ->relationship(name: 'activityCategory', titleAttribute: 'activity_category_name')
                                    ->createOptionForm([
                                        TextInput::make('activity_category_name')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('activity_category_slug', Str::slug($state)))
                                            ->required(),
                                        TextInput::make('activity_category_slug')
                                            ->required()
                                    ]),
                                // ->searchable(),
                                FileUpload::make('activity_image')
                                    ->image()
                                    ->disk('public')->directory('img_activities')
                                    ->required()
                                    ->downloadable()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                        $title = 'activity_' . $get('activity_title') ?: 'activity';

                                        $slug = Str::of($title)
                                            ->lower()
                                            ->replace(' ', '_')
                                            ->slug('_');
                                        return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                                    }),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                DateTimePicker::make('activity_date')
                                    ->label('Activity Date')
                                    ->required(),
                                TextArea::make('activity_location')
                                    ->label('Activity Location')
                                    ->placeholder('Jl. Raya, No. 1, Kelurahan, Kecamatan, Kota, Provinsi')
                                    ->required()
                                    ->maxLength(255),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('activity_image')
                    ->label('IMAGE')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('activity_title')
                    ->label('TITLE')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('activity_location')
                    ->label('LOCATION')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('activity_date')
                    ->dateTime()
                    ->sortable()
                    ->label('DATE')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('activityCategory.activity_category_name')
                    ->searchable()
                    ->sortable()
                    ->label('CATEGORY'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('LOG')
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
                    ->label('Create Activity')
                    ->url(route('filament.management.activities.resources.activities.create'))
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
