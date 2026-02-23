<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms\Components\FileUpload;
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
                TextInput::make('property_type')
                    ->required(),
                TextInput::make('property_price')
                    ->required()
                    ->numeric(),
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
                    ->directory('property_image')
                    ->required()
                    ->multiple()
                    ->maxFiles(10)
                    ->downloadable()
                    ->previewable(true)
                    // ->panelAspectRatio('16:9')
                    // ->reorderable()
                    ->appendFiles()
                    ->helperText('Maksimal 10 file (gambar/video), 100MB per file'),
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
                    ->getStateUsing(fn ($record) => is_array($record->property_image) ? $record->property_image[0] : null)
                    ->size(60),
                TextColumn::make('property_name')
                    ->searchable(),
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
