<?php

namespace App\Filament\Clusters\Letters\Resources;

use App\Filament\Clusters\Letters;
use App\Filament\Clusters\Letters\Resources\LetterCategoryResource\Pages;
use App\Models\LetterCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class LetterCategoryResource extends Resource
{
    protected static ?string $model = LetterCategory::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Letters::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('letter_category_name')
                    ->label('Letter Category Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('letter_category_slug')
                    ->label('Letter Category Slug')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter_category_name')
                    ->label('Name')
                    ->searchable(),
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
            'index' => Pages\ListLetterCategories::route('/'),
            'create' => Pages\CreateLetterCategory::route('/create'),
            'edit' => Pages\EditLetterCategory::route('/{record}/edit'),
        ];
    }
}
