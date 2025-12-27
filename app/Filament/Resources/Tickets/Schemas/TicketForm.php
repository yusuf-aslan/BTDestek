<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Talep Bilgileri')
                    ->schema([
                        TextInput::make('tracking_number')
                            ->label('Takip No')
                            ->disabled(),
                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->disabled(),
                        TextInput::make('department_room')
                            ->label('Bölüm / Oda')
                            ->disabled(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->disabled(),
                        TextInput::make('ip_address')
                            ->label('IP Adresi')
                            ->disabled(),
                        TextInput::make('computer_name')
                            ->label('Bilgisayar Adı')
                            ->disabled(),
                        TextInput::make('subject')
                            ->label('Konu')
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Açıklama')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Tekniker İşlemleri')
                    ->schema([
                        Select::make('status')
                            ->label('Durum')
                            ->options([
                                'yeni' => 'Yeni',
                                'işlemde' => 'İşlemde',
                                'beklemede' => 'Beklemede',
                                'çözüldü' => 'Çözüldü',
                                'iptal' => 'İptal',
                            ])
                            ->required(),
                        Select::make('priority')
                            ->label('Öncelik')
                            ->options([
                                'düşük' => 'Düşük',
                                'orta' => 'Orta',
                                'yüksek' => 'Yüksek',
                                'acil' => 'Acil',
                            ])
                            ->required(),
                        DateTimePicker::make('resolved_at')
                            ->label('Çözüm Tarihi'),
                    ])->columns(2),
            ]);
    }
}
