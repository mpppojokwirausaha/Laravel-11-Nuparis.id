<?php

namespace App\Filament\Clusters\Events\Resources;

use App\Filament\Clusters\Events;
use App\Filament\Clusters\Events\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Events::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('event_title')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('event_slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('event_slug')
                                    ->required()
                                    ->placeholder('Auto Generated'),
                            ])
                            ->columns(2),
                        Group::make()
                            ->schema([
                                RichEditor::make('event_description')
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
                                    ])->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('img_events')
                                    ->fileAttachmentsVisibility('public'),

                                FileUpload::make('event_image')
                                    ->image()
                                    ->disk('public')->directory('img_events')
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
                        Group::make()
                            ->schema([
                                DateTimePicker::make('event_date_start')
                                    ->required(),
                                DateTimePicker::make('event_date_end')
                                    ->required(),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('event_price')
                                    ->required()
                                    ->prefix('Rp ')
                                    ->maxLength(255),
                                Select::make('event_category_uuid')
                                    ->label('Event Category')
                                    ->relationship(name: 'eventCategory', titleAttribute: 'event_category_name')
                                    ->createOptionForm([
                                        TextInput::make('event_category_name')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('event_category_slug', Str::slug($state)))
                                            ->required(),
                                        TextInput::make('event_category_slug')
                                            ->required()
                                    ])
                                    ->required(),
                            ])->columns(2),
                        // ->searchable(),
                        Textarea::make('event_location')
                            ->placeholder('Jl. Raya, No. 1, Kelurahan, Kecamatan, Kota, Provinsi')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('event_image')
                    ->label('IMAGE')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_title')
                    ->label('TITLE')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_price')
                    ->label('PRICE')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('category.event_category_name')
                    ->label('CATEGORY')
                    ->sortable(),
                IconColumn::make('event_status')
                    ->label('STATUS')
                    ->icon(fn(string $state): string => match ($state) {
                        'Active' => 'heroicon-o-check-circle',
                        'Inactive' => 'heroicon-o-x-circle',
                        'Upcoming' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                        'Upcoming' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_date_start')
                    ->label('DATE')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_date_end')
                    ->label('DATE')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                    ->label('Create Event')
                    ->url(route('filament.management.events.resources.events.create'))
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
