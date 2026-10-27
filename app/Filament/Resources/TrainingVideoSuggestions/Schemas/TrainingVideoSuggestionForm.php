<?php

namespace App\Filament\Resources\TrainingVideoSuggestions\Schemas;

use App\Models\TrainingVideoSuggestion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TrainingVideoSuggestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Öneri')
                    ->description(fn (?TrainingVideoSuggestion $record): ?string => $record
                        ? 'İlk istek '.$record->created_at->format('d.m.Y H:i').', son istek '.$record->last_requested_at?->format('d.m.Y H:i')
                        : null)
                    ->schema([
                        TextInput::make('topic')
                            ->label('Konu')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),

                        Select::make('training_video_category_id')
                            ->label('Modül')
                            ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order')->orderBy('name'))
                            ->placeholder('Belirtilmedi')
                            ->searchable()
                            ->preload(),

                        TextInput::make('request_count')
                            ->label('İsteyen Sayısı')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Textarea::make('details')
                            ->label('Açıklama')
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->maxLength(100),

                        TextInput::make('department')
                            ->label('Birim')
                            ->maxLength(100),
                    ])->columns(2),

                Section::make('Takip')
                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Durum')
                            ->options(TrainingVideoSuggestion::STATUSES)
                            ->colors(TrainingVideoSuggestion::STATUS_COLORS)
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'in_progress' => 'heroicon-o-video-camera',
                                'published' => 'heroicon-o-check-circle',
                                'rejected' => 'heroicon-o-x-circle',
                            ])
                            ->inline()
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Select::make('training_video_id')
                            ->label('Hazırlanan Video')
                            ->helperText('Öneri için çekilen videoyu seçin.')
                            ->relationship('video', 'title', fn ($query) => $query->latest())
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => in_array($get('status'), ['in_progress', 'published'], true))
                            ->columnSpanFull(),

                        Textarea::make('admin_note')
                            ->label('İç Not')
                            ->helperText('Yalnızca yöneticiler görür.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
