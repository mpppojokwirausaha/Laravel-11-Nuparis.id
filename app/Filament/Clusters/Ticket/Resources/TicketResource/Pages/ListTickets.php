<?php

namespace App\Filament\Clusters\Ticket\Resources\TicketResource\Pages;

use App\Filament\Clusters\Ticket\Resources\TicketResource;
use App\Models\ConsultantSpecialization;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function getTabs(): array
    {
        return [
            'tickets' => Tab::make('Tickets')
                ->icon('heroicon-m-document-text')
                ->badge(TicketResource::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'categories' => Tab::make('Categories')
                ->icon('heroicon-m-tag')
                ->badge(ConsultantSpecialization::getModel()::count())
                ->modifyQueryUsing(fn(Builder $query) => $query),
        ];
    }

    public function updatedActiveTab(): void
    {
        if ($this->activeTab === 'categories') {
            $this->redirect('/management/ticket/consultant-specializations?activeTab=categories');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
