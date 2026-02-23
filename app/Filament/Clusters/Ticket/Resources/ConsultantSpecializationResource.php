<?php

namespace App\Filament\Clusters\Ticket\Resources;

use App\Filament\Clusters\Ticket;
use App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource\Pages;
use App\Models\ConsultantSpecialization;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;


class ConsultantSpecializationResource extends Resource
{
    protected static ?string $model = ConsultantSpecialization::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Specialist Consultant';
    protected static ?int $navigationSort = 2;
    protected static ?string $cluster = Ticket::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('consultant_specialization_name')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('consultant_specialization_slug', Str::slug($state)))
                    ->required(),
                TextInput::make('consultant_specialization_slug')
                    ->placeholder('Auto Generated'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('consultant_specialization_name')
                    ->label('Specialist Consultant')
                    ->searchable(),
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
            'index' => Pages\ListConsultantSpecializations::route('/'),
            'create' => Pages\CreateConsultantSpecialization::route('/create'),
            'edit' => Pages\EditConsultantSpecialization::route('/{record}/edit'),
        ];
    }
}
