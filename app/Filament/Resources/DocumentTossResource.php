<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTossResource\Pages;
use App\Models\DocumentToss;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTossResource extends Resource
{
    protected static ?string $model = DocumentToss::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'TOSS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticket_code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('document_name')
                    ->label('Document Name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('document_description')
                    ->label('Document Description')
                    ->columnSpanFull(),
                TextInput::make('document_bySign')
                    ->maxLength(255),
                TextInput::make('document_toReceive')
                    ->maxLength(255),
                Select::make('document_action')
                    ->label('Tindakan Dokumen')
                    ->placeholder('Pilih tindakan dokumen')
                    ->options([
                        'Tanda Tangan' => 'Tanda Tangan',
                    ])
                    ->required(),
                TextInput::make('document_no')
                    ->maxLength(255),
                RichEditor::make('document_notes')
                    ->label('Catatan')
                    ->placeholder('Catatan tambahan')
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'strike',
                        'underline',
                    ])->columnSpanFull()
                    ->required(),
                TextInput::make('document_path')
                    ->maxLength(255),
                TextInput::make('qr_path')
                    ->maxLength(255),
                TextInput::make('qr_position_x')
                    ->numeric(),
                TextInput::make('qr_position_y')
                    ->numeric(),
                TextInput::make('qr_scale')
                    ->numeric(),
                TextInput::make('document_final_path')
                    ->required()
                    ->maxLength(255),
                TextInput::make('document_slug')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_code')
                    ->searchable(),
                TextColumn::make('document_no')
                    ->searchable(),
                TextColumn::make('document_name')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('document_bySign')
                    ->searchable(),
                TextColumn::make('document_toReceive')
                    ->searchable(),
                TextColumn::make('document_action')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('LOG')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('visit')
                    ->label('Visit')
                    ->icon('heroicon-o-link')
                    ->url(fn($record) => config('app.url') . '/toss/' . $record->document_slug)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListDocumentTosses::route('/'),
            'create' => Pages\CreateDocumentToss::route('/create'),
            'edit' => Pages\EditDocumentToss::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
