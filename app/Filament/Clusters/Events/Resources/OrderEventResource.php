<?php

namespace App\Filament\Clusters\Events\Resources;

use App\Filament\Clusters\Events;
use App\Filament\Clusters\Events\Resources\OrderEventResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderEventResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Event Order';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Events::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('order_reference_type', 'event')
            ->where('order_id', 'like', 'NUPARIS.ID-EVENT-%');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable(),

                TextColumn::make('order_product_name')
                    ->label('Event Name')
                    ->searchable(),

                TextColumn::make('order_gross_amount')
                    ->label('Total')
                    ->money('idr', true),

                TextColumn::make('order_transaction_status')
                    ->label('Transaction Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'settlement', 'capture', 'success' => 'success',
                        'pending' => 'warning',
                        'deny', 'cancel', 'expire' => 'danger',
                        default => 'gray',
                    }),

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
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListOrderEvents::route('/'),
            'create' => Pages\CreateOrderEvent::route('/create'),
            'edit' => Pages\EditOrderEvent::route('/{record}/edit'),
        ];
    }
}
