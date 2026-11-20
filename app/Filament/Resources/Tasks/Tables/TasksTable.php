<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Builder $query): Builder => $query->byUrgency())
            ->columns([
                IconColumn::make('done')
                    ->label('')
                    ->state(fn (Task $record): bool => $record->isCompleted())
                    ->icon(fn (bool $state): string => $state ? 'heroicon-s-check-circle' : 'heroicon-o-stop-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->size('lg')
                    ->tooltip(fn (Task $record): string => $record->isCompleted() ? 'Geri aç' : 'Tamamlandı olarak işaretle')
                    ->action(fn (Task $record) => static::toggle($record))
                    ->width('1%'),

                TextColumn::make('title')
                    ->label('İş')
                    ->weight('bold')
                    ->color(fn (Task $record): ?string => $record->isCompleted() ? 'gray' : null)
                    ->description(fn (Task $record): ?string => static::summary($record))
                    ->wrap()
                    ->searchable(['title', 'description']),

                ImageColumn::make('photos')
                    ->label('Foto')
                    ->disk(Task::PHOTO_DISK)
                    ->visibility('private')
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->imageHeight(36)
                    ->square()
                    ->toggleable(),

                TextColumn::make('due_at')
                    ->label('Son Tarih')
                    ->formatStateUsing(fn (Task $record): ?string => $record->dueLabel())
                    ->placeholder('Süresiz')
                    ->badge()
                    ->color(fn (Task $record): string => match (true) {
                        $record->isCompleted() => 'gray',
                        $record->isOverdue() => 'danger',
                        $record->isDueToday() => 'warning',
                        default => 'info',
                    })
                    ->icon(fn (Task $record): ?string => $record->isOverdue() ? 'heroicon-m-exclamation-triangle' : ($record->repeat ? 'heroicon-m-arrow-path' : null))
                    ->tooltip(fn (Task $record): ?string => collect([
                        $record->due_at?->translatedFormat('j F Y l H:i'),
                        $record->repeat ? 'Tekrar: '.Task::REPEATS[$record->repeat] : null,
                        $record->remind_before_minutes !== null ? 'Hatırlatma: '.(Task::REMINDERS[$record->remind_before_minutes] ?? '') : null,
                    ])->filter()->implode(' · ') ?: null)
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Task::PRIORITIES[$state] ?? $state)
                    ->color(fn (string $state): string => Task::PRIORITY_COLORS[$state] ?? 'gray')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query
                        ->orderByRaw("CASE priority WHEN 'acil' THEN 0 WHEN 'yüksek' THEN 1 WHEN 'orta' THEN 2 ELSE 3 END ".($direction === 'asc' ? 'asc' : 'desc'))),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Task::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'tamamlandi' => 'success',
                        'devam' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('ticket.tracking_number')
                    ->label('Talep')
                    ->placeholder('—')
                    ->url(fn (Task $record): ?string => $record->ticket_id
                        ? route('filament.admin.resources.tickets.view', $record->ticket_id)
                        : null)
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('completed_at')
                    ->label('Tamamlanma')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options(Task::PRIORITIES),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(Task::STATUSES),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('snooze_hour')
                        ->label('1 saat ertele')
                        ->icon('heroicon-o-clock')
                        ->action(fn (Task $record) => static::snooze($record, '1h')),
                    Action::make('snooze_tomorrow')
                        ->label('Yarına ertele')
                        ->icon('heroicon-o-sun')
                        ->action(fn (Task $record) => static::snooze($record, 'tomorrow')),
                    Action::make('snooze_week')
                        ->label('1 hafta ertele')
                        ->icon('heroicon-o-calendar-days')
                        ->action(fn (Task $record) => static::snooze($record, 'week')),
                ])
                    ->label('Ertele')
                    ->icon('heroicon-o-forward')
                    ->tooltip('Ertele')
                    ->color('gray')
                    ->visible(fn (Task $record): bool => ! $record->isCompleted()),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('complete')
                        ->label('Tamamlandı olarak işaretle')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records
                            ->reject(fn (Task $task) => $task->isCompleted())
                            ->each(fn (Task $task) => $task->complete())),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('Burada iş yok')
            ->emptyStateDescription('Yapacağınız işleri ekleyin, son tarih verin. Tarihi yaklaşınca ya da geçince sağ üstteki zilden hatırlatılırsınız.')
            ->emptyStateActions([
                CreateAction::make()->label('İş Ekle'),
            ]);
    }

    private static function toggle(Task $task): void
    {
        if ($task->isCompleted()) {
            $task->reopen();

            return;
        }

        $next = $task->complete();

        Notification::make()
            ->title('İş tamamlandı')
            ->body($next ? 'Bir sonraki tekrar oluşturuldu: '.$next->dueLabel() : null)
            ->success()
            ->send();
    }

    private static function snooze(Task $task, string $interval): void
    {
        $task->snooze($interval);

        Notification::make()
            ->title('Ertelendi')
            ->body('Yeni son tarih: '.$task->dueLabel())
            ->success()
            ->send();
    }

    /** Ayrıntının ilk satırı ve alt adım ilerlemesi. */
    private static function summary(Task $task): ?string
    {
        $progress = $task->checklistProgress();

        return collect([
            $progress ? "✓ {$progress['done']}/{$progress['total']} adım" : null,
            $task->description ? str(html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '</li>'], ' ', $task->description))))->squish()->limit(90)->toString() : null,
        ])->filter()->implode(' · ') ?: null;
    }
}
