<?php

namespace App\Filament\Resources\TicketAttachments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TicketAttachmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dosya Yükleme')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Dosya Seçin')
                            ->disk('public')
                            ->directory('attachments')
                            ->visibility('public')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state && is_object($state)) {
                                    $set('file_name', $state->getClientOriginalName());
                                    $set('file_type', $state->getMimeType());
                                    $set('file_size', $state->getSize());
                                }
                            })
                            ->columnSpanFull(),
                        
                        Select::make('ticket_id')
                            ->label('İlgili Talep (Opsiyonel)')
                            ->relationship('ticket', 'tracking_number')
                            ->searchable()
                            ->preload()
                            ->placeholder('Dosyayı bir talebe bağlayabilirsiniz'),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                Section::make('Dosya Detayları')
                    ->schema([
                        TextInput::make('file_name')
                            ->label('Dosya Adı')
                            ->required()
                            ->disabled(fn ($livewire) => !($livewire instanceof \Filament\Resources\Pages\CreateRecord)),
                        
                        TextInput::make('file_type')
                            ->label('Dosya Türü')
                            ->required()
                            ->disabled(fn ($livewire) => !($livewire instanceof \Filament\Resources\Pages\CreateRecord)),
                        
                        TextInput::make('file_size')
                            ->label('Dosya Boyutu (Byte)')
                            ->numeric()
                            ->required()
                            ->disabled(fn ($livewire) => !($livewire instanceof \Filament\Resources\Pages\CreateRecord)),

                        Placeholder::make('ticket_link')
                            ->label('Bağlı Olduğu Talep')
                            ->content(fn ($record) => $record && $record->ticket ? new HtmlString('<a href="/admin/tickets/'.$record->ticket_id.'/edit" class="text-blue-600 font-bold underline">'.$record->ticket->tracking_number.'</a>') : 'Bağlı talep yok')
                            ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                        Placeholder::make('preview')
                            ->label('Görüntüleme')
                            ->columnSpanFull()
                            ->content(fn ($record) => $record && str_contains($record->file_type, 'image') 
                                ? new HtmlString('<img src="/storage/'.$record->file_path.'" class="max-w-md rounded-lg shadow-lg">')
                                : new HtmlString('<div class="p-4 bg-gray-100 rounded-lg text-gray-500 text-sm italic">Bu dosya türü için önizleme mevcut değil.</div>'))
                            ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                    ])->columns(2)
            ]);
    }
}
