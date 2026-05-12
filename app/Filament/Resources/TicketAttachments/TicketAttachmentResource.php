<?php

namespace App\Filament\Resources\TicketAttachments;

use App\Filament\Resources\TicketAttachments\Pages\CreateTicketAttachment;
use App\Filament\Resources\TicketAttachments\Pages\EditTicketAttachment;
use App\Filament\Resources\TicketAttachments\Pages\ListTicketAttachments;
use App\Filament\Resources\TicketAttachments\Schemas\TicketAttachmentForm;
use App\Filament\Resources\TicketAttachments\Tables\TicketAttachmentsTable;
use App\Models\TicketAttachment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketAttachmentResource extends Resource
{
    protected static ?string $model = TicketAttachment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $navigationLabel = 'Ortam Kütüphanesi';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?string $modelLabel = 'Ek / Dosya';


    protected static ?string $pluralModelLabel = 'Ortam Kütüphanesi';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool

    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return TicketAttachmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketAttachmentsTable::configure($table);
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
            'index' => ListTicketAttachments::route('/'),
            'create' => CreateTicketAttachment::route('/create'),
        ];
    }
}
