<?php

namespace App\Filament\Consultations\Clusters\Tickets\Resources;

use App\Filament\Consultations\Clusters\Tickets;
use App\Filament\Consultations\Clusters\Tickets\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Tickets::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticket_code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ticket_title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('ticket_content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('ticket_colsultant')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ticket_status')
                    ->required(),
                TextInput::make('ticket_document_support')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                TextColumn::make('ticket_code')
                    ->searchable(),
                TextColumn::make('ticket_title')
                    ->searchable(),
                TextColumn::make('ticket_colsultant')
                    ->searchable(),
                TextColumn::make('ticket_status'),
                TextColumn::make('ticket_document_support')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
