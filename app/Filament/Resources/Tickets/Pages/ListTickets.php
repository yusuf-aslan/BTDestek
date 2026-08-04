<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TicketStatus;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getPollingInterval(): ?string
    {
        return '10s';
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Bekleyenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', TicketStatus::keysFor('pending')))
                ->badge($this->getStatusCount(TicketStatus::keysFor('pending')))
                ->badgeColor('warning'),
            
            'solved' => Tab::make('Çözülenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', TicketStatus::keysFor('solved')))
                ->badge($this->getStatusCount(TicketStatus::keysFor('solved')))
                ->badgeColor('success'),

            'cancelled' => Tab::make('İptal Edilenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', TicketStatus::keysFor('cancelled')))
                ->badge($this->getStatusCount(TicketStatus::keysFor('cancelled')))
                ->badgeColor('danger'),

            'all' => Tab::make('Tümü')
                ->badge($this->getStatusCount()),
        ];
    }

    protected function getStatusCount(?array $statuses = null): ?int
    {
        $query = static::getResource()::getEloquentQuery();
        
        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        return $query->count();
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }
}
