<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TechnicianPerformanceTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Teknisyen Performans Raporu';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('categories') // Only technicians
                    ->withCount([
                        'tickets as active_tickets' => fn (Builder $query) => $query->whereNotIn('status', ['çözüldü', 'iptal']),
                        'resolvedTickets as resolved_today' => fn (Builder $query) => $query->where('status', 'çözüldü')->whereDate('resolved_at', today()),
                        'resolvedTickets as total_resolved' => fn (Builder $query) => $query->where('status', 'çözüldü'),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Teknisyen')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('active_tickets')
                    ->label('Aktif İşleri')
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'danger' : ($state > 0 ? 'warning' : 'gray'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_today')
                    ->label('Bugün Çözülen')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_resolved')
                    ->label('Toplam Çözülen')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ]);
    }
}
