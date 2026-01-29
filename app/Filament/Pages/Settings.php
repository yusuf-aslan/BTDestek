<?php

namespace App\Filament\Pages;

use App\Models\GeneralSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        return auth()->user()->hasModuleAccess('settings');
    }

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Genel Ayarlar';

    protected static ?string $navigationLabel = 'Ayarlar';

    public static function getNavigationGroup(): ?string
    {
        return 'Sistem';
    }

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = GeneralSetting::firstOrCreate([]);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Settings')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Genel Ayarlar')
                            ->icon('heroicon-o-computer-desktop')
                            ->schema([
                                Section::make('Site Kimliği')
                                    ->schema([
                                        TextInput::make('site_title')
                                            ->label('Site Başlığı')
                                            ->required(),
                                        
                                        FileUpload::make('site_logo')
                                            ->label('Site Logosu')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public'),

                                        FileUpload::make('site_favicon')
                                            ->label('Site Favicon')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public'),
                                    ])->columns(2),

                                Section::make('Görünüm')
                                    ->schema([
                                        Toggle::make('is_dark_mode_enabled')
                                            ->label('Gece/Gündüz Modu')
                                            ->helperText('Açık ise kullanıcılar sağ üst köşeden karanlık moda geçiş yapabilir.')
                                            ->default(true),
                                        Toggle::make('show_email_on_ticket_form')
                                            ->label('Talep Formunda E-posta Alanını Göster')
                                            ->helperText('Açık ise talep oluşturma formunda e-posta alanı gösterilir.')
                                            ->default(false),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Mesai & Kurallar')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Çalışma Saatleri')
                                    ->description('Mesai saatleri ve talep kabul kuralları.')
                                    ->schema([
                                        TimePicker::make('work_hours_start')
                                            ->label('Mesai Başlangıcı')
                                            ->seconds(false)
                                            ->default('08:00')
                                            ->required(),

                                        TimePicker::make('work_hours_end')
                                            ->label('Mesai Bitişi')
                                            ->seconds(false)
                                            ->default('17:00')
                                            ->required(),

                                        Toggle::make('allow_tickets_outside_work_hours')
                                            ->label('Mesai Dışı Talep Kabul Et')
                                            ->helperText('Kapalı ise belirtilen saatler dışında talep açılamaz.')
                                            ->default(true),

                                        Toggle::make('weekend_tickets_allowed')
                                            ->label('Hafta Sonu Talep Kabul Et')
                                            ->helperText('Kapalı ise Cumartesi ve Pazar günleri talep açılamaz.')
                                            ->default(true),
                                    ])->columns(2),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('İçerik Yönetimi')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Önemli Hatırlatmalar')
                                    ->description('Anasayfa sağ tarafta görünen hatırlatma notları.')
                                    ->schema([
                                        Repeater::make('important_reminders')
                                            ->label('Hatırlatmalar')
                                            ->schema([
                                                TextInput::make('text')
                                                    ->label('Hatırlatma Metni')
                                                    ->required(),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                                            ->defaultItems(3)
                                            ->reorderableWithButtons()
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('E-Posta (SMTP)')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Section::make('SMTP Sunucu Ayarları')
                                    ->description('Sistem bildirimlerinin gönderileceği sunucu ayarları.')
                                    ->schema([
                                                                TextInput::make('mail_host')
                                                                    ->label('SMTP Sunucusu')
                                                                    ->placeholder('smtp.example.com'),
                                                                
                                                                TextInput::make('mail_port')
                                                                    ->label('SMTP Port')
                                                                    ->placeholder('587')
                                                                    ->numeric(),
                                        TextInput::make('mail_username')
                                            ->label('Kullanıcı Adı')
                                            ->placeholder('user@example.com'),

                                        TextInput::make('mail_password')
                                            ->label('Şifre')
                                            ->password()
                                            ->revealable(),
                                        
                                        TextInput::make('mail_encryption')
                                            ->label('Şifreleme')
                                            ->placeholder('tls'),

                                        TextInput::make('mail_from_address')
                                            ->label('Gönderen Adresi')
                                            ->email()
                                            ->placeholder('noreply@example.com'),

                                        TextInput::make('mail_from_name')
                                            ->label('Gönderen Adı')
                                            ->placeholder('BT Destek Sistemi'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpan('full'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Ayarları Kaydet')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        GeneralSetting::first()->update($data);

        Notification::make()
            ->success()
            ->title('Ayarlar kaydedildi.')
            ->send();
    }
}
