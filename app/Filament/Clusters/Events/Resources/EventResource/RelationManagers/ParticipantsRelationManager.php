<?php

namespace App\Filament\Clusters\Events\Resources\EventResource\RelationManagers;

use App\Exports\ParticipantsExport;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\Action;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $title = 'Daftar Partisipan';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('participant_name')
            ->columns([
                TextColumn::make('participant_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participant_email')
                    ->label('Email')
                    ->searchable()
                    ->url(function ($record) {
                        if (!$record->participant_email) return null;

                        $eventTitle = $this->getOwnerRecord()->event_title;
                        $subject = $eventTitle;

                        return "mailto:{$record->participant_email}?subject=" . rawurlencode($subject);
                    })
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('info'),
                TextColumn::make('participant_no_wa')
                    ->label('WhatsApp')
                    ->formatStateUsing(fn($state) => $state ?? '-')
                    ->url(fn($record) => $record->participant_no_wa ? "https://wa.me/{$record->participant_no_wa}" : null)
                    ->icon('heroicon-m-chat-bubble-oval-left')->iconColor('success')
                    ->openUrlInNewTab(),
                TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->badge()
                    ->color('success'),
                IconColumn::make('checked_in')
                    ->label('Check-in')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->checked_in_at !== null),
                TextColumn::make('created_at')
                    ->label('Tgl Registrasi')
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('company')
                    ->label('Perusahaan'),
                TextColumn::make('source')
                    ->label('Sumber Info'),
            ])
            ->filters([
                SelectFilter::make('check_in_status')
                    ->label('Status Check-in')
                    ->options([
                        'checked' => 'Sudah Check-in',
                        'not_checked' => 'Belum Check-in',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'checked') {
                            $query->whereNotNull('checked_in_at');
                        } elseif ($data['value'] === 'not_checked') {
                            $query->whereNull('checked_in_at');
                        }
                    }),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $eventUuid = $this->getOwnerRecord()->uuid;
                        return Excel::download(new ParticipantsExport($eventUuid), 'participants_' . date('Y-m-d_His') . '.xlsx');
                    }),

            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_export')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            $eventUuid = $this->getOwnerRecord()->uuid;
                            $selectedIds = $records->pluck('uuid')->toArray();
                            return Excel::download(
                                (new ParticipantsExport($eventUuid))->query()->whereIn('uuid', $selectedIds),
                                'participants_selected_' . date('Y-m-d_His') . '.xlsx'
                            );
                        }),
                ]),
            ]);
    }
}
