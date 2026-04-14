<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\CannedResponse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
                        TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->disabled(),
                        TextInput::make('department_room')
                            ->label('Bölüm')
                            ->disabled(),
                        TextInput::make('phone_number')
                            ->label('Dahili No / Tel')
                            ->disabled(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Select::make('asset_id')
                            ->label('İlgili Envanter / Cihaz')
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->searchable()
                            ->preload()
                            ->placeholder('Envanter Seçiniz (Opsiyonel)'),
                        TextInput::make('ip_address')
                            ->label('Talep Yazılan PC IP')
                            ->disabled(),
                        TextInput::make('broken_pc_ip')
                            ->label('Arızalı PC IP (Zorunlu Değil)')
                            ->disabled(),

                        TextInput::make('subject')
                            ->label('Talep Başlığı (Kısaca)')
                            ->placeholder('Örn: Bilgisayar açılmıyor, Hasta taburcu edilemiyor vb.')
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Detaylı Açıklama')
                            ->disabled()
                            ->rows(10)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Tekniker İşlemleri')
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Atanan Teknisyen')
                            ->relationship('assignedTo', 'name', function (\Illuminate\Database\Eloquent\Builder $query, Get $get) {
                                $categoryId = $get('category_id');
                                if ($categoryId) {
                                    $hasCategoryUsers = \App\Models\User::whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))->exists();
                                    if ($hasCategoryUsers) {
                                        return $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
                                    }
                                }
                                return $query;
                            })
                            ->searchable()
                            ->preload()
                            ->live(),
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
                        
                        Select::make('canned_response_id')
                            ->label('Hazır Cevap Şablonu')
                            ->options(CannedResponse::all()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $response = CannedResponse::find($state);
                                    if ($response) {
                                        $set('resolution_note', $response->content);
                                    }
                                }
                            })
                            ->columnSpanFull(),
                            
                        Textarea::make('resolution_note')
                            ->label('Çözüm Notu / Açıklama')
                            ->rows(10)
                            ->columnSpanFull(),

                        DatePicker::make('resolved_at')
                            ->label('Çözüm Tarihi'),
                    ])->columns(2),

                Section::make('Ekler')
                    ->schema([
                        \Filament\Forms\Components\ViewField::make('attachments_gallery')
                            ->label('Yüklenen Görseller')
                            ->view('filament.forms.components.attachment-gallery')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && $record->attachments()->where('file_type', 'like', 'image/%')->count() > 0),

                        \Filament\Forms\Components\Repeater::make('attachments')
                            ->label('Tüm Dosyalar')
                            ->relationship()
                            ->schema([
                                TextInput::make('file_name')
                                    ->label('Dosya Adı')
                                    ->disabled()
                                    ->columnSpan(3),
                                TextInput::make('file_size')
                                    ->label('Boyut')
                                    ->formatStateUsing(fn ($state) => round($state / 1024) . ' KB')
                                    ->disabled(),
                                \Filament\Forms\Components\Placeholder::make('download_link')
                                    ->label('İşlem')
                                    ->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString('<a href="'.route('tickets.attachments.download', $record).'" target="_blank" class="text-primary-600 hover:text-primary-500 font-bold underline">İndir</a>') : '-')
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->columns(5)
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record && $record->attachments()->count() > 0),

                Section::make('Dahili Notlar')
                    ->description('Sadece teknisyenlerin görebileceği özel notlar.')
                    ->collapsible()
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('notes')
                            ->relationship()
                            ->schema([
                                Textarea::make('note')
                                    ->label('Not')
                                    ->required()
                                    ->rows(2),
                                \Filament\Forms\Components\Hidden::make('user_id')
                                    ->default(Auth::id()),
                            ])
                            ->label('Notlar')
                            ->createItemButtonLabel('Yeni Not Ekle')
                            ->reorderable(false)
                    ]),

                Section::make('Talep Geçmişi')
                    ->description('Talebin yaşam döngüsü ve yapılan işlemler.')
                    ->collapsible()
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('activities')
                            ->relationship()
                            ->schema([
                                TextInput::make('description')
                                    ->label('İşlem')
                                    ->disabled(),
                                TextInput::make('created_at')
                                    ->label('Tarih')
                                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d.m.Y H:i'))
                                    ->disabled(),
                                TextInput::make('user.name')
                                    ->label('Yapan')
                                    ->placeholder('Sistem')
                                    ->disabled(),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(3)
                            ->label('İşlem Kayıtları')
                    ]),
            ]);
    }
}
