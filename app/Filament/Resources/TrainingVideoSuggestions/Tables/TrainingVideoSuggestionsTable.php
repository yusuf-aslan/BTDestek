<?php

namespace App\Filament\Resources\TrainingVideoSuggestions\Tables;

use App\Models\TrainingVideoSuggestion;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TrainingVideoSuggestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['category', 'video']))
            ->defaultSort('request_count', 'desc')
            ->columns([
                TextColumn::make('topic')
                    ->label('Konu')
                    ->searchable(['topic', 'details'])
                    ->wrap()
                    ->description(fn (TrainingVideoSuggestion $record): ?string => $record->details ? (string) str($record->details)->limit(100) : null),

                TextColumn::make('category.name')
                    ->label('Modül')
                    ->placeholder('Belirtilmedi')
                    ->toggleable(),

                TextColumn::make('request_count')
                    ->label('İsteyen')
                    ->badge()
                    ->color(fn (int $state): string => $state >= 5 ? 'danger' : ($state > 1 ? 'warning' : 'gray'))
                    ->formatStateUsing(fn (int $state): string => $state.' kişi')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TrainingVideoSuggestion::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => TrainingVideoSuggestion::STATUS_COLORS[$state] ?? 'gray')
                    ->description(fn (TrainingVideoSuggestion $record): ?string => $record->video?->title),

                TextColumn::make('department')
                    ->label('Birim')
                    ->description(fn (TrainingVideoSuggestion $record): ?string => $record->name)
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('last_requested_at')
                    ->label('Son İstek')
                    ->since()
                    ->dateTimeTooltip('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('İlk İstek')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(TrainingVideoSuggestion::STATUSES)
                    ->multiple()
                    ->default(TrainingVideoSuggestion::OPEN_STATUSES),

                SelectFilter::make('training_video_category_id')
                    ->label('Modül')
                    ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order')->orderBy('name'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('watch')
                    ->label('Videoyu İzle')
                    ->icon('heroicon-o-play')
                    ->color('gray')
                    ->visible(fn (TrainingVideoSuggestion $record): bool => $record->video !== null)
                    ->url(fn (TrainingVideoSuggestion $record): ?string => $record->video ? route('training.show', $record->video) : null)
                    ->openUrlInNewTab(),
                EditAction::make(),
                ActionGroup::make(self::statusActions())
                    ->label('Durum')
                    ->icon('heroicon-o-flag')
                    ->tooltip('Durumu değiştir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('merge')
                        ->label('Birleştir')
                        ->icon('heroicon-o-arrows-pointing-in')
                        ->requiresConfirmation()
                        ->modalDescription('Seçilen öneriler en çok istenen öneride toplanır: isteyen sayıları toplanır, diğer önerilerin konusu ve açıklaması açıklamaya eklenir, kalanlar silinir.')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => self::merge($records)),

                    BulkAction::make('set_status')
                        ->label('Durum Değiştir')
                        ->icon('heroicon-o-flag')
                        ->schema([
                            Select::make('status')
                                ->label('Durum')
                                ->options(TrainingVideoSuggestion::STATUSES)
                                ->required(),
                        ])
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records, array $data) => TrainingVideoSuggestion::query()
                            ->whereKey($records->modelKeys())
                            ->update(['status' => $data['status']])),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function statusActions(): array
    {
        return collect(TrainingVideoSuggestion::STATUSES)
            ->map(fn (string $label, string $status) => Action::make('status_'.$status)
                ->label($label)
                ->color(TrainingVideoSuggestion::STATUS_COLORS[$status])
                ->hidden(fn (TrainingVideoSuggestion $record): bool => $record->status === $status)
                ->action(fn (TrainingVideoSuggestion $record) => $record->update(['status' => $status])))
            ->values()
            ->all();
    }

    private static function merge(Collection $records): void
    {
        if ($records->count() < 2) {
            Notification::make()->title('Birleştirmek için en az iki öneri seçin.')->warning()->send();

            return;
        }

        DB::transaction(function () use ($records) {
            $sorted = $records->sortByDesc('request_count')->values();
            $target = $sorted->first();
            $others = $sorted->slice(1);

            $details = collect([$target->details])
                ->merge($others->map(fn (TrainingVideoSuggestion $s) => $s->topic.($s->details ? ': '.$s->details : '')))
                ->filter()
                ->unique()
                ->implode("\n\n");

            $target->update([
                'request_count' => $sorted->sum('request_count'),
                'details' => $details ?: null,
                'last_requested_at' => $sorted->max('last_requested_at'),
            ]);

            TrainingVideoSuggestion::query()->whereKey($others->modelKeys())->delete();
        });

        Notification::make()->title('Öneriler birleştirildi')->success()->send();
    }
}
