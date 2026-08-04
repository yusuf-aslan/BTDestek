<?php
namespace App\Filament\Resources\TicketStatuses\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
class TicketStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Durum Ayarları')->description('Durumun adını, görünümünü ve talep kapatıldığında hangi sekmede yer alacağını belirleyin.')->schema([
                TextInput::make('name')->label('Görünen Ad')->required()->maxLength(80)->live(onBlur:true)->afterStateUpdated(fn($state,$set,$record)=>$record ?: $set('key',Str::lower(Str::squish($state)))),
                TextInput::make('key')->label('Sistem Anahtarı')->helperText('Kayıt oluşturulduktan sonra değiştirilemez.')->required()->maxLength(64)->unique(ignoreRecord:true)->disabledOn('edit')->dehydrated(),
                Select::make('destination')->label('Gösterileceği Sekme')->options(['pending'=>'Bekleyenler','solved'=>'Çözülenler','cancelled'=>'İptal Edilenler'])->required(),
                Select::make('color')->label('Etiket Rengi')->options(['gray'=>'Gri','primary'=>'Mor / Ana Renk','info'=>'Mavi','warning'=>'Turuncu','success'=>'Yeşil','danger'=>'Kırmızı'])->required(),
                TextInput::make('sort_order')->label('Sıralama')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('closes_ticket')->label('Bu durum talebi kapatsın')->helperText('İşaretlenince kayıt otomatik olarak Çözülenler sekmesine gider; bekleme süresi durur, çözüm tarihi ve kapatan teknisyen kaydedilir.')->live()->afterStateUpdated(fn($state,$set)=>$state ? $set('destination','solved') : null),
                Toggle::make('is_active')->label('Durum seçim listesinde aktif')->default(true),
            ])->columns(2),
        ]);
    }
}
