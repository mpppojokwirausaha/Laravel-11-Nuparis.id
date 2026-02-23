<?php

namespace App\Filament\Clusters\Ticket\Resources;

use \App\Models\TicketStatus;
use App\Filament\Clusters\Ticket;
use App\Filament\Clusters\Ticket\Resources\TicketResource\Pages;
use App\Models\Ticket as TicketModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = TicketModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Ticket::class;
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticket_code')
                    ->disabled()
                    ->required(),

                TextInput::make('ticket_title')
                    ->disabled()
                    ->required(),

                TextInput::make('ticket_whatsapp')
                    ->disabled()
                    ->required(),

                TextInput::make('ticket_email')
                    ->disabled()
                    ->required(),

                Textarea::make('ticket_content')
                    ->disabled()
                    ->rows(18)
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('ticket_document_support')
                    ->label('View Dokumen Pendukung Client')
                    ->disk('public')
                    ->directory('ticket/initial-report_client')
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->preserveFilenames()
                    ->disabled() // tidak bisa diganti
                    ->dehydrated(false) // supaya tidak disimpan ulang
                    ->columnSpanFull(),
                Select::make('consultant_specialization_uuid')
                    ->label('Spesialisasi')
                    ->relationship('consultantSpecialization', 'consultant_specialization_name')
                    ->required()
                    ->preload()
                    ->disabled(),

                Select::make('ticket_status_uuid')
                    ->label('Status')
                    ->relationship('ticketStatus', 'ticket_status_name')
                    ->live()
                    ->preload()
                    ->required(),
                View::make('front-end.layouts.components.progress-note')
                    ->label('Riwayat Progress')
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) =>
                        optional(TicketStatus::find($get('ticket_status_uuid')))->ticket_status_name !== 'open'),

                Group::make([
                    RichEditor::make('progress')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    FileUpload::make('progress_files')
                        ->label('Upload File (Opsional)')
                        ->disk('public')
                        ->directory(fn ($get) => 'tickets/' . $get('ticket_code'))
                        ->multiple()
                        ->nullable()
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames(),
                    ])
                    ->columnSpanFull()
                    ->columns(1)
                    ->hidden(fn (callable $get) =>
                        optional(TicketStatus::find($get('ticket_status_uuid')))->ticket_status_name !== 'open'),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_code')
                    ->label('Code')
                    ->searchable(),

                TextColumn::make('ticket_title')
                    ->label('Title')
                    ->searchable(),

                TextColumn::make('consultantSpecialization.consultant_specialization_name')
                    ->label('Spesialisasi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ticketStatus.ticket_status_name')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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

    public static function canDelete($record): bool
    {
        return false;
    }
}