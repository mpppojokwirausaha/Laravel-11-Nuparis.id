<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrustedHubResource\Pages;
use App\Models\TrustedHub;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class TrustedHubResource extends Resource
{
    protected static ?string $model = TrustedHub::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?string $navigationLabel = 'Trusted Hub';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('trustedhub_url')
                    ->name('Url')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Textarea::make('trustedhub_description')
                    ->name('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')
                    ->rowIndex()
                    ->label('No'),
                TextColumn::make('trustedhub_url')
                    ->label('URL')
                    ->searchable(),
                TextColumn::make('trustedhub_description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('log')
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
            'index' => Pages\ListTrustedHubs::route('/'),
            'create' => Pages\CreateTrustedHub::route('/create'),
            'edit' => Pages\EditTrustedHub::route('/{record}/edit'),
        ];
    }
}
