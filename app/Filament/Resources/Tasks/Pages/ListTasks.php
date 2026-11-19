<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('İş Ekle')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $query = fn () => TaskResource::getEloquentQuery();

        return [
            'active' => Tab::make('Yapılacaklar')
                ->icon('heroicon-o-list-bullet')
                ->modifyQueryUsing(fn (Builder $query) => $query->open())
                ->badge($query()->open()->count() ?: null),

            'overdue' => Tab::make('Gecikenler')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue())
                ->badge($query()->overdue()->count() ?: null)
                ->badgeColor('danger'),

            'today' => Tab::make('Bugün')
                ->icon('heroicon-o-sun')
                ->modifyQueryUsing(fn (Builder $query) => $query->open()->whereDate('due_at', today()))
                ->badge($query()->open()->whereDate('due_at', today())->count() ?: null)
                ->badgeColor('warning'),

            'week' => Tab::make('Bu Hafta')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => $query->open()->whereBetween('due_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])),

            'done' => Tab::make('Tamamlananlar')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'tamamlandi')->reorder()->latest('completed_at')),

            'all' => Tab::make('Tümü'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'active';
    }
}
