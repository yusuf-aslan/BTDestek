<?php

namespace App\Filament\Resources\Tickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ticket;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable()
                    ->sortable(),
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
                    ->toggleable(),
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
                    ->color(fn (string $state): string => match ($state) {
                        'yeni' => 'gray',
                        'işlemde' => 'warning',
                        'beklemede' => 'info',
                        'çözüldü' => 'success',
                        'iptal' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Atanan')
                    ->placeholder('Atanmadı')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Bekleme Süresi')
                    ->formatStateUsing(function ($state, $record) {
                        $endTime = ($record->status === 'çözüldü' || $record->status === 'iptal') ? ($record->resolved_at ?? $record->updated_at) : now();
                        $diff = \Carbon\Carbon::parse($state)->diff($endTime);
                        if ($diff->d > 0) return "{$diff->d} gün {$diff->h} sa";
                        if ($diff->h > 0) return "{$diff->h} saat {$diff->i} dk";
                        return "{$diff->i} dakika";
                    })
                    ->color(function ($record) {
                        if ($record->status === 'çözüldü' || $record->status === 'iptal') return 'gray';
                        $hours = \Carbon\Carbon::parse($record->created_at)->diffInHours(now());
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
                EditAction::make(),
                Action::make('claim')
                    ->label('Üzerime Al')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->action(fn (Ticket $record) => $record->update(['assigned_to' => auth()->id()]))
                    ->visible(fn (Ticket $record) => $record->assigned_to !== auth()->id() && $record->status !== 'çözüldü' && $record->status !== 'iptal'),
                Action::make('assign')
                    ->label('Bileti Ata')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\Select::make('assigned_to')
                            ->label('Teknisyen Seç')
                            ->options(function (Ticket $record) {
                                $users = \App\Models\User::whereHas('categories', fn ($query) => $query->where('categories.id', $record->category_id))
                                    ->pluck('name', 'id');
                                
                                if ($users->isEmpty()) {
                                    return \App\Models\User::all()->pluck('name', 'id');
                                }
                                
                                return $users;
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(fn (Ticket $record, array $data) => $record->update($data))
                    ->visible(fn (Ticket $record) => $record->status !== 'çözüldü' && $record->status !== 'iptal'),
                Action::make('print')
                    ->label('Yazdır')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => route('admin.tickets.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
