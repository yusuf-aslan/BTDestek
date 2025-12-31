<?php

namespace App\Filament\Resources\AnnouncementTemplates;

use App\Filament\Resources\AnnouncementTemplates\Pages\ManageAnnouncementTemplates;
use App\Models\AnnouncementTemplate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementTemplateResource extends Resource
{
    protected static ?string $model = AnnouncementTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    public static function getNavigationGroup(): ?string
    {
        return 'Duyuru Yönetimi';
    }

    protected static ?string $navigationLabel = 'Duyuru Şablonları';

    protected static ?string $modelLabel = 'Duyuru Şablonu';

    protected static ?string $pluralModelLabel = 'Duyuru Şablonları';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('announcements');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Şablon Başlığı')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Duyuru Türü')
                    ->options([
                        'info' => 'Bilgilendirme (Mavi)',
                        'warning' => 'Uyarı (Sarı)',
                        'danger' => 'Kritik (Kırmızı)',
                    ])
                    ->required()
                    ->native(false),
                Textarea::make('content')
                    ->label('İçerik')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('type'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'danger' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'Bilgilendirme',
                        'warning' => 'Uyarı',
                        'danger' => 'Kritik',
                    }),
                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncementTemplates::route('/'),
        ];
    }
}
