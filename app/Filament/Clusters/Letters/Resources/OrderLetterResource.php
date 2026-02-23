<?php

namespace App\Filament\Clusters\Letters\Resources;

use App\Filament\Clusters\Letters;
use App\Filament\Clusters\Letters\Resources\OrderLetterResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\DeleteBulkAction;


class OrderLetterResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Letter Order';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Letters::class;
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

    protected function getBulkActions(): array
    {
        return [];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable(),
                TextColumn::make('order_letter_name')
                    ->label('Letter Name')
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
            'index' => Pages\ListOrderLetters::route('/'),
            'create' => Pages\CreateOrderLetter::route('/create'),
            'edit' => Pages\EditOrderLetter::route('/{record}/edit'),
        ];
    }
}
