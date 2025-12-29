<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Duyuru Detayları')
                    ->schema([
                        Select::make('template_id')
                            ->label('Şablondan Yükle')
                            ->options(\App\Models\AnnouncementTemplate::all()->pluck('title', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                $template = \App\Models\AnnouncementTemplate::find($state);
                                if ($template) {
                                    $set('title', $template->title);
                                    $set('content', $template->content);
                                    $set('type', $template->type);
                                }
                            })
                            ->columnSpanFull()
                            ->placeholder('Bir şablon seçerek alanları otomatik doldurabilirsiniz...'),
                        
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255),
                            
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
                            ->default(true),
                            
                        Textarea::make('content')
                            ->label('İçerik')
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('save_as_template')
                            ->label('Bu duyuruyu şablon olarak kaydet')
                            ->columnSpanFull()
                            ->visible(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Announcements\Pages\CreateAnnouncement),
                    ])->columns(2)
            ]);
    }
}