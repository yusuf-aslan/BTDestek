<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tatil Bilgisi')
                    ->description('Birden çok günlük bayramları tek kayıtta girin; arifeyi yarım gün olarak ayrı kayıt açın.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ad')
                            ->placeholder('Örn: Kurban Bayramı, Ramazan Bayramı Arifesi, İdari İzin')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label('Başlangıç')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->firstDayOfWeek(1)
                            ->required()
                            ->live(onBlur: true),

                        DatePicker::make('end_date')
                            ->label('Bitiş')
                            ->helperText('Tek günse boş bırakın.')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->firstDayOfWeek(1)
                            ->afterOrEqual('start_date')
                            ->minDate(fn (Get $get) => $get('start_date')),

                        Toggle::make('is_half_day')
                            ->label('Yarım Gün')
                            ->helperText('Arifeler için. Kapanış saatine kadar talep alınır.')
                            ->default(false)
                            ->live()
                            ->inline(false),

                        TimePicker::make('closes_at')
                            ->label('Kapanış Saati')
                            ->seconds(false)
                            ->default('13:00')
                            ->required(fn (Get $get): bool => (bool) $get('is_half_day'))
                            ->visible(fn (Get $get): bool => (bool) $get('is_half_day')),

                        Textarea::make('message')
                            ->label('Kullanıcıya Gösterilecek Mesaj')
                            ->helperText('Boş bırakılırsa: "Talepler yalnızca mesai saatleri içinde alınmaktadır." Mesai saatleri altında ayrıca gösterilir.')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
