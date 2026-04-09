<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Filament\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $recordTitleAttribute = 'subject';

    protected static ?string $modelLabel = 'Talep';
    protected static ?string $pluralModelLabel = 'Talepler';
    protected static ?string $navigationLabel = 'Talepler';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereNotIn('status', ['çözüldü', 'iptal'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user) {
            // Check if the user has any assigned categories
            $hasAssignedCategories = $user->categories()->exists();
            
            if ($hasAssignedCategories) {
                // Filter tickets where the category is assigned to the user
                $query->whereHas('category', function (Builder $query) use ($user) {
                    $query->whereHas('users', function (Builder $query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
                });
            } elseif (!$user->is_admin) {
                // If not admin and no categories, they shouldn't see anything
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
        ];
    }
}
