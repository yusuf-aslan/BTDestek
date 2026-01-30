<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cihaz Kimliği')
                    ->schema([
                        TextInput::make('name')
                            ->label('Cihaz Adı / Kodu')
                            ->placeholder('Örn: BILGI-ISLEM-PC-01')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('asset_tag')
                            ->label('Demirbaş No')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('serial_number')
                            ->label('Seri Numarası')
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Cihaz Türü')
                            ->options([
                                'computer' => 'Bilgisayar (PC/Laptop)',
                                'printer' => 'Yazıcı / Tarayıcı',
                                'network' => 'Ağ Cihazı (Switch/Modem)',
                                'monitor' => 'Monitör',
                                'tablet' => 'Tablet / Telefon',
                                'medical' => 'Tıbbi Cihaz PC',
                                'other' => 'Diğer',
                            ])
                            ->default('computer')
                            ->required(),
                        Select::make('status')
                            ->label('Durum')
                            ->options([
                                'active' => 'Aktif / Kullanımda',
                                'stock' => 'Depoda / Yedek',
                                'maintenance' => 'Serviste / Bakımda',
                                'retired' => 'Hurda / Kullanım Dışı',
                                'broken' => 'Arızalı',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                    ->collapsible(),

                Section::make('Konum ve Sahiplik')
                    ->schema([
                        Select::make('location_id')
                            ->relationship('location', 'anabirim')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->anabirim} / {$record->altbirim}")
                            ->label('Bölüm / Birim')
                            ->searchable()
                            ->preload(),
                        Select::make('assigned_user_id')
                            ->relationship('assignedUser', 'name')
                            ->label('Zimmetli Personel')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Teknik Detaylar')
                    ->schema([
                        TextInput::make('brand')
                            ->label('Marka')
                            ->placeholder('Dell, HP, Lenovo...'),
                        TextInput::make('model')
                            ->label('Model')
                            ->placeholder('Optiplex 3080...'),
                        KeyValue::make('specs')
                            ->label('Teknik Özellikler')
                            ->keyLabel('Özellik (Örn: RAM)')
                            ->valueLabel('Değer (Örn: 16GB)')
                            ->addActionLabel('Özellik Ekle')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Finansal Bilgiler')
                    ->schema([
                        DatePicker::make('purchase_date')
                            ->label('Satın Alma Tarihi'),
                        DatePicker::make('warranty_expires_at')
                            ->label('Garanti Bitiş Tarihi'),
                        Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }
}
