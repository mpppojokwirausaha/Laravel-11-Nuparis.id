<?php

namespace App\Filament\Clusters\Ticket\Resources\TicketResource\Pages;

use App\Filament\Clusters\Ticket\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Decode jika ticket_progress masih berupa string JSON
        $progressNotes = $this->record->ticket_progress;

        // Tambahkan progress baru jika ada
        if (!empty($data['progress']) || !empty($data['progress_files'])) {
            $progressNotes[] = [
                'progress' => $data['progress'] ?? '',
                'file' => $data['progress_files'] ?? [],
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ];
        }

        $data['ticket_progress'] = $progressNotes;

        // Hapus field input sementara
        unset($data['progress'], $data['progress_files']);

        return $data;
    }

}
