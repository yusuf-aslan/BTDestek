<?php

namespace App\Filament\Resources\DeviceTemplates;

use App\Filament\Resources\DeviceTemplates\Pages\CreateDeviceTemplate;
use App\Filament\Resources\DeviceTemplates\Pages\EditDeviceTemplate;
use App\Filament\Resources\DeviceTemplates\Pages\ListDeviceTemplates;
use App\Models\DeviceTemplate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeviceTemplateResource extends Resource
{
    protected static ?string $model = DeviceTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    public static function getNavigationGroup(): ?string
    {
        return 'Envanter Yönetimi';
    }

    protected static ?string $modelLabel = 'Cihaz Şablonu';

    protected static ?string $pluralModelLabel = 'Cihaz Şablonları';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Şablon Adı')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                KeyValue::make('specs')
                    ->label('Teknik Özellikler Şablonu')
                    ->keyLabel('Özellik (Örn: RAM)')
                    ->valueLabel('Varsayılan Değer (Örn: 16GB)')
                    ->default([
                        'RAM' => '',
                        'Monitör' => '',
                    ])
                    ->addActionLabel('Özellik Ekle')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Şablon Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('specs')
                    ->label('Özellikler')
                    ->formatStateUsing(fn ($state) => is_array($state) ? collect($state)->map(fn ($value, $key) => "{$key}: {$value}")->implode(', ') : $state),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListDeviceTemplates::route('/'),
            'create' => CreateDeviceTemplate::route('/create'),
            'edit' => EditDeviceTemplate::route('/{record}/edit'),
        ];
    }
}
