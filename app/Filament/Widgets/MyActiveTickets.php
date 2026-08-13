<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MyActiveTickets extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Bekleyen Talepleriniz';

    protected static ?int $sort = 1;

    public static function canView(): bool { return false; }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->visibleTo(Auth::user())
                    ->whereIn('status', \App\Models\TicketStatus::keysFor('pending'))
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('department_room')
                    ->label('Bölüm')
                    ->limit(30),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'acil' => 'danger',
                        'yüksek' => 'warning',
                        'orta' => 'info',
                        'düşük' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(fn (Ticket $record): string => route('filament.admin.resources.tickets.edit', $record));
    }
}
