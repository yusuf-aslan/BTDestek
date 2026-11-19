<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Models\Task;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Kişiye özel iş listesi. Yönetici dahil herkes yalnızca kendi işlerini görür.
 */
class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'İş';

    protected static ?string $pluralModelLabel = 'İş Listem';

    protected static ?string $navigationLabel = 'İş Listem';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ownedBy(Filament::auth()->user());
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::urgentCount();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()->overdue()->exists() ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Süresi geçen ve bugün bitmesi gereken işler';
    }

    /** Süresi geçen + bugün bitecek açık işler. */
    public static function urgentCount(): int
    {
        return static::getEloquentQuery()->open()->whereNotNull('due_at')->where('due_at', '<=', now()->endOfDay())->count();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Durum' => Task::STATUSES[$record->status] ?? null,
            'Bitiş' => $record->dueLabel(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
