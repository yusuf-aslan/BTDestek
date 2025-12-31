<?php

namespace App\Filament\Resources\CannedResponses;

use App\Filament\Resources\CannedResponses\Pages\CreateCannedResponse;
use App\Filament\Resources\CannedResponses\Pages\EditCannedResponse;
use App\Filament\Resources\CannedResponses\Pages\ListCannedResponses;
use App\Filament\Resources\CannedResponses\Schemas\CannedResponseForm;
use App\Filament\Resources\CannedResponses\Tables\CannedResponsesTable;
use App\Models\CannedResponse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CannedResponseResource extends Resource
{
    protected static ?string $model = CannedResponse::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Hazır Cevap';
    protected static ?string $pluralModelLabel = 'Hazır Cevaplar';
    protected static ?string $navigationLabel = 'Hazır Cevaplar';

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('canned_responses');
    }

    public static function form(Schema $schema): Schema
    {
        return CannedResponseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CannedResponsesTable::configure($table);
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
            'index' => ListCannedResponses::route('/'),
            'create' => CreateCannedResponse::route('/create'),
            'edit' => EditCannedResponse::route('/{record}/edit'),
        ];
    }
}