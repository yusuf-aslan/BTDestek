<?php

namespace App\Filament\Resources\Holidays\Tables;

use App\Models\Holiday;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HolidaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date')
            ->columns([
                TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->description(fn (Holiday $record): ?string => $record->message ? (string) str($record->message)->limit(80) : null),

                TextColumn::make('start_date')
                    ->label('Tarih')
                    ->formatStateUsing(fn (Holiday $record): string => $record->dateRangeLabel())
                    ->description(fn (Holiday $record): string => $record->start_date->translatedFormat('l'))
                    ->sortable(),

                TextColumn::make('closes_at')
                    ->label('Süre')
                    ->state(fn (Holiday $record): string => $record->is_half_day
                        ? 'Yarım gün ('.substr((string) $record->closes_at, 0, 5).')'
                        : 'Tam gün')
                    ->badge()
                    ->color(fn (Holiday $record): string => $record->is_half_day ? 'warning' : 'gray'),

                TextColumn::make('end_date')
                    ->label('Durum')
                    ->state(fn (Holiday $record): string => match (true) {
                        $record->end_date->lt(today()) => 'Geçti',
                        $record->start_date->lte(today()) => 'Bugün',
                        default => 'Yaklaşan',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Bugün' => 'danger',
                        'Yaklaşan' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Geçmiş günleri gizle')
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query) => $query->whereDate('end_date', '>=', today())),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
