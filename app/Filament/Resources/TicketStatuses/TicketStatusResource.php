<?php
namespace App\Filament\Resources\TicketStatuses;
use App\Filament\Resources\TicketStatuses\Pages\CreateTicketStatus;
use App\Filament\Resources\TicketStatuses\Pages\EditTicketStatus;
use App\Filament\Resources\TicketStatuses\Pages\ListTicketStatuses;
use App\Filament\Resources\TicketStatuses\Schemas\TicketStatusForm;
use App\Filament\Resources\TicketStatuses\Tables\TicketStatusesTable;
use App\Models\TicketStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
class TicketStatusResource extends Resource
{
    protected static ?string $model = TicketStatus::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $modelLabel = 'Talep Durumu';
    protected static ?string $pluralModelLabel = 'Talep Durumları';
    protected static ?string $navigationLabel = 'Durum Yönetimi';
    protected static string|\UnitEnum|null $navigationGroup = 'Talep Yönetimi';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'name';
    public static function canAccess(): bool { return (bool) auth()->user()?->is_admin; }
    public static function form(Schema $schema): Schema { return TicketStatusForm::configure($schema); }
    public static function table(Table $table): Table { return TicketStatusesTable::configure($table); }
    public static function getPages(): array { return ['index'=>ListTicketStatuses::route('/'),'create'=>CreateTicketStatus::route('/create'),'edit'=>EditTicketStatus::route('/{record}/edit')]; }
}
