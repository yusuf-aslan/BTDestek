<?php

namespace App\Filament\Resources\TrainingVideoSuggestions;

use App\Filament\Resources\TrainingVideoSuggestions\Pages\EditTrainingVideoSuggestion;
use App\Filament\Resources\TrainingVideoSuggestions\Pages\ListTrainingVideoSuggestions;
use App\Filament\Resources\TrainingVideoSuggestions\Schemas\TrainingVideoSuggestionForm;
use App\Filament\Resources\TrainingVideoSuggestions\Tables\TrainingVideoSuggestionsTable;
use App\Models\TrainingVideoSuggestion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TrainingVideoSuggestionResource extends Resource
{
    protected static ?string $model = TrainingVideoSuggestion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Video Önerileri';

    protected static string|\UnitEnum|null $navigationGroup = 'Bilgi ve Eğitim';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Video Önerisi';

    protected static ?string $pluralModelLabel = 'Video Önerileri';

    protected static ?string $recordTitleAttribute = 'topic';

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('training_videos');
    }

    public static function canCreate(): bool
    {
        // Öneriler destek sayfasından gelir
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = TrainingVideoSuggestion::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Bekleyen öneri';
    }

    public static function form(Schema $schema): Schema
    {
        return TrainingVideoSuggestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingVideoSuggestionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingVideoSuggestions::route('/'),
            'edit' => EditTrainingVideoSuggestion::route('/{record}/edit'),
        ];
    }
}
