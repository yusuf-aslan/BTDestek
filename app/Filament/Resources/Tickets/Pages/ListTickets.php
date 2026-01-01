<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Bekleyenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('status', ['çözüldü', 'iptal']))
                ->badge($this->getStatusCount(['yeni', 'işlemde', 'beklemede']))
                ->badgeColor('warning'),
            
            'solved' => Tab::make('Çözülenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'çözüldü'))
                ->badge($this->getStatusCount(['çözüldü']))
                ->badgeColor('success'),

            'cancelled' => Tab::make('İptal Edilenler')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'iptal'))
                ->badge($this->getStatusCount(['iptal']))
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
