<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
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


class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('property_name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('property_slug', Str::slug($state)))
                    ->required(),
                TextInput::make('property_slug')
                    ->required()
                    ->placeholder('Auto Generated'),

                Group::make([
                    TextInput::make('property_type')
                        ->required(),
                    TextInput::make('property_price')
                        ->required()
                        ->prefix('Rp ')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            if ($state !== null) {
                                $component->state(number_format($state, 0, ',', '.'));
                            }
                        })
                        ->dehydrateStateUsing(function ($state) {
                            return $state ? str_replace('.', '', $state) : null;
                        })
                        ->rule('integer'),
                    DateTimePicker::make('property_date_end')
                        ->required(),

                ])->columns(3)->columnSpanFull(),
                Textarea::make('property_address')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('property_latitude'),
                TextInput::make('property_longitude'),
                RichEditor::make('property_description')
                    ->toolbarButtons([
                        'bold',
                        'bulletList',
                        'link',
                        'orderedList',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull()
                    ->required(),

                FileUpload::make('property_image')
                    ->label('Gambar/Video Properti')
                    ->acceptedFileTypes(['image/*', 'video/*'])
                    ->disk('public')
                    ->directory('img_properties')
                    ->required()
                    ->multiple()
                    ->maxFiles(10)
                    ->downloadable()
                    ->previewable(true)
                    // ->panelAspectRatio('16:9')
                    // ->reorderable()
                    ->appendFiles()
                    ->helperText('Maksimal 10 file (gambar/video), 100MB per file')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                        $name = 'property_' . $get('property_name') ?: 'property';

                        $slug = Str::of($name)
                            ->lower()
                            ->replace(' ', '_')
                            ->slug('_');

                        return $slug . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    }),
                TextInput::make('property_land_area')
                    ->required(),
                TextInput::make('property_building_area')
                    ->required(),
                TextInput::make('property_fasilities'),
                TextInput::make('property_certificate'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('property_image')
                    ->label('Image')
                    ->getStateUsing(fn($record) => is_array($record->property_image) ? $record->property_image[0] : null)
                    ->size(60)
                    ->circular(),
                TextColumn::make('property_name')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
                TextColumn::make('property_type')
                    ->searchable(),
                TextColumn::make('property_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Log')
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
