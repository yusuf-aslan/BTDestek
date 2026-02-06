<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kişisel Bilgiler')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required(),
                        
                        TextInput::make('email')
                            ->label('E-posta Adresi')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                    ])->columns(2),

                Section::make('Yetkilendirme')
                    ->description('Kullanıcının sistemdeki rolü ve sorumlu olduğu alanlar.')
                    ->schema([
                        Toggle::make('is_admin')
                            ->label('Sistem Yöneticisi (Admin)')
                            ->helperText('Bu yetki verildiğinde kullanıcı tüm talepleri görebilir ve sistem ayarlarına erişebilir.')
                            ->default(false)
                            ->columnSpanFull()
                            ->live(),

                        \Filament\Forms\Components\CheckboxList::make('accessible_modules')
                            ->label('Ekstra Modül Yetkileri')
                            ->helperText('Admin olmayan kullanıcıların erişebileceği ek modüller.')
                            ->options([
                                'announcements' => 'Duyurular',
                                'announcement_templates' => 'Duyuru Şablonları',
                                'articles' => 'Bilgi Bankası',
                                'assets' => 'Varlık Yönetimi',
                                'canned_responses' => 'Hazır Cevaplar',
                                'categories' => 'Kategoriler',
                                'departments' => 'Departmanlar',
                                'locations' => 'Konumlar',
                                'menus' => 'Menüler',
                                'settings' => 'Site Ayarları',
                                'tickets' => 'Talepler',
                                'users' => 'Personel Yönetimi',
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('is_admin')),

                        Select::make('categories')
                            ->label('Sorumlu Olduğu Kategoriler')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText('Kullanıcı sadece seçili kategorilere ait talepleri görebilecektir (Admin değilse).'),
                    ]),
            ]);
    }
}