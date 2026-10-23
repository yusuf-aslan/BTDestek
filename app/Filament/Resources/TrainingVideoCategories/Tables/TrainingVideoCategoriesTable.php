<?php

namespace App\Filament\Resources\TrainingVideoCategories\Tables;

use App\Filament\Resources\TrainingVideos\TrainingVideoResource;
use App\Models\TrainingVideoCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TrainingVideoCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('videos'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ColorColumn::make('color')
                    ->label('Renk'),

                TextColumn::make('name')
                    ->label('Modül')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TrainingVideoCategory $record): ?string => $record->description ? (string) str($record->description)->limit(80) : null),

                TextColumn::make('videos_count')
                    ->label('Video')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                Action::make('videos')
                    ->label('Videolar')
                    ->icon('heroicon-o-play-circle')
                    ->color('gray')
                    ->url(fn (TrainingVideoCategory $record): string => TrainingVideoResource::getUrl('index', [
                        'filters' => ['training_video_category_id' => ['value' => $record->getKey()]],
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
