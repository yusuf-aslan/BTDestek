<?php

namespace App\Filament\Resources\Tickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ticket;
use App\Models\TicketStatus;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                TextColumn::make('name')
                    ->label('Açan Kullanıcı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject')
                    ->label('Konu')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Envanter')
                    ->searchable()
                    ->placeholder('-')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_number')
                    ->label('Dahili')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'düşük' => 'gray',
                        'orta' => 'info',
                        'yüksek' => 'warning',
                        'acil' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketStatus::labelFor($state))
                    ->color(fn (string $state): string => TicketStatus::colorFor($state))
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Atanan')
                    ->placeholder('Atanmadı')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Bekleme Süresi')
                    ->formatStateUsing(function ($state, $record) {
                        $endTime = TicketStatus::isClosed($record->status) ? ($record->resolved_at ?? $record->updated_at) : now();
                        $minutes = $record->elapsedMinutes($endTime);
                        $days = intdiv($minutes, 1440);
                        $hours = intdiv($minutes % 1440, 60);
                        $remainingMinutes = $minutes % 60;

                        if ($days > 0) return "{$days} gün {$hours} sa";
                        if ($hours > 0) return "{$hours} saat {$remainingMinutes} dk";
                        return "{$remainingMinutes} dakika";
                    })
                    ->color(function ($record) {
                        if (TicketStatus::isClosed($record->status)) return 'gray';
                        $hours = $record->elapsedMinutes() / 60;
                        if ($hours >= 24) return 'danger';
                        if ($hours >= 2) return 'warning';
                        return 'success';
                    })
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->format('d.m.Y H:i')),
            ])
            ->filters([
                SelectFilter::make('assigned_to')
                    ->label('Atanan')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options([
                        'düşük' => 'Düşük',
                        'orta' => 'Orta',
                        'yüksek' => 'Yüksek',
                        'acil' => 'Acil',
                    ]),
                Filter::make('created_at')
                    ->label('Tarih Aralığı')
                    ->form([
                        DatePicker::make('from')
                            ->label('Başlangıç Tarihi')
                            ->placeholder('GG.AA.YYYY'),
                        DatePicker::make('until')
                            ->label('Bitiş Tarihi')
                            ->placeholder('GG.AA.YYYY'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Başlangıç: ' . \Carbon\Carbon::parse($data['from'])->format('d.m.Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Bitiş: ' . \Carbon\Carbon::parse($data['until'])->format('d.m.Y');
                        }
                        return $indicators;
                    })
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('claim')
                        ->label('Üzerime Al')
                        ->icon('heroicon-o-hand-raised')
                        ->color('success')
                        ->action(fn (Ticket $record) => $record->update(['assigned_to' => auth()->id()]))
                        ->visible(fn (Ticket $record) => $record->assigned_to !== auth()->id() && !TicketStatus::isClosed($record->status)),
                    Action::make('assign')
                        ->label('Bileti Ata')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('category_id')
                                ->label('Kategori Seç')
                                ->options(\App\Models\Category::query()->orderBy('name')->pluck('name', 'id'))
                                ->default(fn (Ticket $record) => $record->category_id)
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (\Filament\Schemas\Components\Utilities\Set $set) => $set('assigned_to', null)),
                            \Filament\Forms\Components\Select::make('assigned_to')
                                ->label('Teknisyen Seç')
                                ->options(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                    $categoryId = $get('category_id');
                                    $users = \App\Models\User::query()
                                        ->when($categoryId, fn ($query) => $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->where('categories.id', $categoryId)))
                                        ->pluck('name', 'id');
                                    
                                    if ($users->isEmpty()) {
                                        return \App\Models\User::query()->orderBy('name')->pluck('name', 'id');
                                    }
                                    
                                    return $users;
                                })
                                ->required()
                                ->searchable()
                                ->preload(),
                        ])
                        ->action(fn (Ticket $record, array $data) => $record->update($data))
                        ->visible(fn (Ticket $record) => !TicketStatus::isClosed($record->status)),
                    Action::make('print')
                        ->label('Yazdır')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(fn ($record) => route('admin.tickets.print', $record))
                        ->openUrlInNewTab(),
                ])
                ->label('İşlemler')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
            ])
            ->recordAction(null)
            ->defaultSort('created_at', 'desc');
    }
}
