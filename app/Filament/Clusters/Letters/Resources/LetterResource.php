<?php

namespace App\Filament\Clusters\Letters\Resources;

use App\Filament\Clusters\Letters;
use App\Filament\Clusters\Letters\Resources\LetterResource\Pages;
use App\Models\Letter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LetterResource extends Resource
{
    protected static ?string $model = Letter::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Letters::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('letter_name')
                    ->name('Letter Name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('letter_slug', Str::slug($state)))
                    ->required(),
                TextInput::make('letter_slug')
                    ->name('Letter Slug')
                    ->placeholder('Auto Generated'),
                Textarea::make('letter_desc')
                    ->name('Letter Description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('letter_price')
                    ->name('Letter Price')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                    ->prefix('Rp ')
                    ->required()
                    ->numeric(),
                Select::make('letter_category_uuid')
                    ->label('Letter Category')
                    ->relationship(name: 'letterCategory', titleAttribute: 'letter_category_name')
                    ->createOptionForm([
                        TextInput::make('letter_category_name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('letter_category_slug', Str::slug($state)))
                            ->required(),
                        TextInput::make('letter_category_slug')
                            ->required()
                    ])->required(),
                FileUpload::make('letter_file_path')
                    ->disk('public')
                    ->directory('letters')
                    ->required()
                    ->downloadable()
                    ->label('Letter File (DOC/XLSM)')
                    ->getUploadedFileNameForStorageUsing(function ($file, $record) {
                        $name = str($record?->letter_slug ?? request()->input('letter_slug'))->slug('-');
                        $extension = $file->getClientOriginalExtension();
                        if (empty($name)) {
                            $name = 'document';
                        }
                        return "{$name}-" . time() . ".{$extension}";
                    })
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('letterCategory.letter_category_name')
                    ->label('Category')
                    ->searchable(),
                TextColumn::make('letter_price')
                    ->label('Price')
                    ->money('idr', true),
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
            'index' => Pages\ListLetters::route('/'),
            'create' => Pages\CreateLetter::route('/create'),
            'edit' => Pages\EditLetter::route('/{record}/edit'),
        ];
    }
}
