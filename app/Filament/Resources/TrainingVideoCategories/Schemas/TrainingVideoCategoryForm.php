<?php

namespace App\Filament\Resources\TrainingVideoCategories\Schemas;

use App\Models\TrainingVideoCategory;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TrainingVideoCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Modül Bilgileri')
                    ->description('Destek sayfasında videolar bu modüllere göre listelenir.')
                    ->schema(self::fields())
                    ->columns(2),
            ]);
    }

    /**
     * Video formundaki "yeni modül" penceresi kısa sürümü kullanır; slug ve sıra modelde otomatik atanır.
     */
    public static function fields(bool $compact = false): array
    {
        $fields = [
            TextInput::make('name')
                ->label('Modül Adı')
                ->placeholder('ör. Talep Oluşturma, Stok, Personel')
                ->required()
                ->maxLength(100)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state, string $operation) use ($compact) {
                    // Paylaşılan bağlantılar bozulmasın diye slug yalnızca oluştururken üretilir
                    if (! $compact && $operation === 'create') {
                        $set('slug', Str::slug((string) $state));
                    }
                }),

            ColorPicker::make('color')
                ->label('Renk')
                ->helperText('Modül listesindeki renkli nokta.')
                ->default(fn (): string => TrainingVideoCategory::PALETTE[TrainingVideoCategory::query()->count() % count(TrainingVideoCategory::PALETTE)])
                ->regex('/^#[0-9a-fA-F]{6}$/')
                ->required(),
        ];

        if ($compact) {
            return $fields;
        }

        return [
            ...$fields,

            TextInput::make('slug')
                ->label('Slug')
                ->helperText('Bağlantıda kullanılır: /egitim-videolari?modul=slug')
                ->required()
                ->alphaDash()
                ->maxLength(120)
                ->unique(ignoreRecord: true),

            TextInput::make('sort_order')
                ->label('Sıra')
                ->helperText('Küçük sayı önce gösterilir. Listede sürükleyerek de sıralayabilirsiniz.')
                ->numeric()
                ->minValue(0)
                ->default(fn (): int => (int) TrainingVideoCategory::query()->max('sort_order') + 1)
                ->required(),

            Textarea::make('description')
                ->label('Açıklama')
                ->placeholder('Bu modülün videolarında neler anlatıldığını kısaca yazın.')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->helperText('Pasif modül ve videoları destek sayfasında gösterilmez.')
                ->default(true),
        ];
    }
}
