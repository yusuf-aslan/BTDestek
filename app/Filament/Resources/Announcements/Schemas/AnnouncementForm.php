<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Hızlı Başlangıç')
                    ->description('Hazır bir şablon kullanarak duyuru oluşturabilirsiniz.')
                    ->schema([
                        Select::make('template_id')
                            ->label('Şablon Seçiniz')
                            ->options(\App\Models\AnnouncementTemplate::all()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                $template = \App\Models\AnnouncementTemplate::find($state);
                                if ($template) {
                                    $set('title', $template->title);
                                    $set('content', $template->content);
                                    $set('type', $template->type);
                                }
                            })
                            ->columnSpanFull()
                            ->placeholder('Sıfırdan oluşturmak için boş bırakın veya bir şablon seçin...'),
                    ])
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Duyuru İçeriği')
                    ->schema([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                            
                        Select::make('type')
                            ->label('Tür')
                            ->options([
                                'info' => 'Bilgi (Mavi)',
                                'warning' => 'Uyarı (Turuncu)',
                                'danger' => 'Kritik (Kırmızı)',
                            ])
                            ->required(),

                        \Filament\Forms\Components\DateTimePicker::make('published_at')
                            ->label('Yayınlanma Tarihi')
                            ->default(now())
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Aktif mi?')
                            ->default(true)
                            ->inline(false),
                            
                        Textarea::make('content')
                            ->label('İçerik')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('save_as_template')
                            ->label('Bu duyuruyu gelecekte kullanmak üzere şablon olarak kaydet')
                            ->columnSpanFull()
                            ->visible(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Announcements\Pages\CreateAnnouncement),
                    ])->columns(2)
            ]);
    }
}