<?php

namespace App\Filament\Resources\TrainingVideos;

use App\Filament\Resources\TrainingVideos\Pages\CreateTrainingVideo;
use App\Filament\Resources\TrainingVideos\Pages\EditTrainingVideo;
use App\Filament\Resources\TrainingVideos\Pages\ListTrainingVideos;
use App\Filament\Resources\TrainingVideos\Schemas\TrainingVideoForm;
use App\Filament\Resources\TrainingVideos\Tables\TrainingVideosTable;
use App\Models\TrainingVideo;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TrainingVideoResource extends Resource
{
    protected static ?string $model = TrainingVideo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationLabel = 'Eğitim Videoları';

    protected static string|\UnitEnum|null $navigationGroup = 'Talep Yönetimi';
    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Eğitim Videosu';

    protected static ?string $pluralModelLabel = 'Eğitim Videoları';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('training_videos');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainingVideoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingVideosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingVideos::route('/'),
            'create' => CreateTrainingVideo::route('/create'),
            'edit' => EditTrainingVideo::route('/{record}/edit'),
        ];
    }
}
