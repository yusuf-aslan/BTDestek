<?php

namespace App\Livewire;

use App\Models\Ticket;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class MyActiveTickets extends TableWidget
{
    protected static ?string $heading = 'Aktif Taleplerim';

    protected int | string | array $columnSpan = 'full';

    #[On('refresh-active-tickets')]
    public function refresh(): void
    {
        // Redraw the table
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->whereNotIn('status', ['çözüldü', 'iptal'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Talep Sahibi')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(30),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'yeni' => 'info',
                        'işlemde' => 'warning',
                        'beklemede' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ]);
    }
}
