<?php

namespace App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource\Pages;

use App\Filament\Clusters\Ticket\Resources\ConsultantSpecializationResource;
use App\Filament\Clusters\Ticket\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConsultantSpecializations extends ListRecords
{
    protected static string $resource = ConsultantSpecializationResource::class;

    public function getTabs(): array
    {
        return [
            'tickets' => Tab::make('Tickets')
                ->icon('heroicon-m-document-text')
                ->badge(TicketResource::getModel()::count()),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ConsultantSpecializationResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'tickets') {
            $this->redirect('/management/ticket/tickets');
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
