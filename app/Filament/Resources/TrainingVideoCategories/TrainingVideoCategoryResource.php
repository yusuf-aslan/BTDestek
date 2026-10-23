<?php

namespace App\Filament\Resources\TrainingVideoCategories;

use App\Filament\Resources\TrainingVideoCategories\Pages\CreateTrainingVideoCategory;
use App\Filament\Resources\TrainingVideoCategories\Pages\EditTrainingVideoCategory;
use App\Filament\Resources\TrainingVideoCategories\Pages\ListTrainingVideoCategories;
use App\Filament\Resources\TrainingVideoCategories\Schemas\TrainingVideoCategoryForm;
use App\Filament\Resources\TrainingVideoCategories\Tables\TrainingVideoCategoriesTable;
use App\Models\TrainingVideoCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TrainingVideoCategoryResource extends Resource
{
    protected static ?string $model = TrainingVideoCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Video Modülleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Talep Yönetimi';

    protected static ?string $navigationParentItem = 'Eğitim Videoları';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'Video Modülü';

    protected static ?string $pluralModelLabel = 'Video Modülleri';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('training_videos');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainingVideoCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingVideoCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingVideoCategories::route('/'),
            'create' => CreateTrainingVideoCategory::route('/create'),
            'edit' => EditTrainingVideoCategory::route('/{record}/edit'),
        ];
    }
}
