<?php

namespace App\Filament\Resources\TrainingVideos\Tables;

use App\Models\TrainingVideo;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TrainingVideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->defaultSort('created_at', 'desc')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('poster_path')
                    ->label('Kapak')
                    ->disk(TrainingVideo::POSTER_DISK)
                    ->imageHeight(40),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TrainingVideo $record): ?string => $record->category?->name),

                TextColumn::make('audience')
                    ->label('Kimler Görebilir')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'staff' ? 'Personel' : 'Herkes')
                    ->color(fn (string $state): string => $state === 'staff' ? 'warning' : 'success'),

                TextColumn::make('source_type')
                    ->label('Kaynak')
                    ->formatStateUsing(fn (string $state): string => $state === 'url' ? 'Bağlantı' : 'Dosya')
                    ->icon(fn (string $state): string => $state === 'url' ? 'heroicon-o-link' : 'heroicon-o-film')
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('views')
                    ->label('İzlenme')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Eklendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('training_video_category_id')
                    ->label('Modül')
                    ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order')->orderBy('name'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('audience')
                    ->label('Kimler Görebilir')
                    ->options(['public' => 'Herkes', 'staff' => 'Personel']),

                SelectFilter::make('is_published')
                    ->label('Durum')
                    ->options([
                        '1' => 'Yayında',
                        '0' => 'Taslak',
                    ]),
            ])
            ->recordActions([
                Action::make('watch')
                    ->label('İzle')
                    ->icon('heroicon-o-play')
                    ->color('gray')
                    ->url(fn (TrainingVideo $record): string => route('training.show', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
