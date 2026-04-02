<?php

namespace App\Filament\Clusters\Events\Resources;

use App\Filament\Clusters\Events;
use App\Filament\Clusters\Events\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Events::class;
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

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
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('event_slug')
                                    ->required()
                                    ->placeholder('Auto Generated')
                                    ->maxLength(255),
                                TextInput::make('event_quota')
                                    ->required()
                                    ->placeholder('200')
                                    ->numeric()
                                    ->minValue(0),
                                Toggle::make('event_is_active')
                                    ->label('Status Regist')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->live()
                                    ->disabled(
                                        fn($get) =>
                                        $get('event_date_end') &&
                                            Carbon::parse($get('event_date_end'))->isPast()
                                    )
                                    ->afterStateHydrated(function ($set, $get) {
                                        $endDate = $get('event_date_end');
                                        if ($endDate && Carbon::parse($endDate)->isPast()) {
                                            $set('event_is_active', false);
                                        }
                                    }),
                            ])
                            ->columns(4),
                        Group::make()
                            ->schema([
                                TextInput::make('event_price')
                                    ->required()
                                    ->placeholder('0')
                                    ->prefix('Rp ')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxLength(255),
                                Select::make('event_category_uuid')
                                    ->label('Event Category')
                                    ->relationship(name: 'eventCategory', titleAttribute: 'event_category_name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('event_category_name')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('event_category_slug', Str::slug($state)))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('event_category_slug')
                                            ->required()
                                            ->maxLength(255)
                                    ])
                                    ->required(),
                                TextInput::make('event_link')
                                    ->label('Event Link Whatsapp Group')
                                    ->url()
                                    ->placeholder('https://chat.whatsapp.com/KODE_UNDANGAN')
                                    ->prefix('https://')
                                    ->maxLength(255)
                                    ->required(),
                            ])->columns(3),
                        Group::make()
                            ->schema([
                                DateTimePicker::make('event_date_start')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->format('Y-m-d H:i:s'),
                                DateTimePicker::make('event_date_end')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->format('Y-m-d H:i:s')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state && Carbon::parse($state)->isPast()) {
                                            $set('event_is_active', false);
                                        }
                                    }),
                                Select::make('event_type')
                                    ->label('Event Type')
                                    ->placeholder('Pilih minimal 1 opsi')
                                    ->multiple()
                                    ->options([
                                        'Online' => 'Online',
                                        'Offline' => 'Offline',
                                    ])
                                    ->required(),
                            ])->columns(3),
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
                                    ])
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('img_events')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpan(1),

                                FileUpload::make('event_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('img_events')
                                    ->required()
                                    ->downloadable()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                        $title = $get('event_title') ?? 'event';
                                        $slug = Str::of($title)
                                            ->lower()
                                            ->replace(' ', '_')
                                            ->slug('_');
                                        return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                                    })
                                    ->columnSpan(1),
                            ])->columns(2),
                        Textarea::make('event_location')
                            ->placeholder('Jl. Raya, No. 1, Kelurahan, Kecamatan, Kota, Provinsi')
                            ->required()
                            ->maxLength(255)
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('event_image')
                    ->label('IMAGE')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->width(50)
                    ->height(50),
                TextColumn::make('event_title')
                    ->label('TITLE')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_price')
                    ->label('PRICE')
                    ->searchable()
                    ->sortable()
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('eventCategory.event_category_name')
                    ->label('CATEGORY')
                    ->sortable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_quota')
                    ->label('QUOTA')
                    ->sortable()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),

                // STATUS PENDAFTARAN
                IconColumn::make('event_is_active')
                    ->label('STATUS REGIST')
                    ->icon(fn(bool $state): string => match ($state) {
                        true => 'heroicon-o-check-circle',
                        false => 'heroicon-o-x-circle',
                    })
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'danger',
                    })
                    ->tooltip(fn(bool $state): string => match ($state) {
                        true => 'Pendaftaran Dibuka',
                        false => 'Pendaftaran Ditutup',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                // STATUS EVENT (Berdasarkan tanggal)
                TextColumn::make('event_status')
                    ->label('STATUS EVENT')
                    ->badge()
                    ->getStateUsing(function ($record): string {
                        $now = Carbon::now();
                        $startDate = Carbon::parse($record->event_date_start);
                        $endDate = Carbon::parse($record->event_date_end);

                        if ($now->lt($startDate)) {
                            return 'Upcoming';
                        } elseif ($now->between($startDate, $endDate)) {
                            return 'Active';
                        } else {
                            return 'Inactive';
                        }
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                        'Upcoming' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Active' => 'heroicon-o-play-circle',
                        'Inactive' => 'heroicon-o-stop-circle',
                        'Upcoming' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('event_type')
                    ->label('TYPE')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state): string => is_array($state) ? implode(', ', $state) : $state)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_date_start')
                    ->label('START DATE')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('event_date_end')
                    ->label('END DATE')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('CREATED AT')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('UPDATED AT')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label(''),
                EditAction::make()
                    ->label(''),
                DeleteAction::make()
                    ->label(''),
                Action::make('visit_event')
                    ->label('Visit')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn($record): string => route('event-detail', ['event_slug' => $record->event_slug]))
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->tooltip('View this event on the public website'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
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
