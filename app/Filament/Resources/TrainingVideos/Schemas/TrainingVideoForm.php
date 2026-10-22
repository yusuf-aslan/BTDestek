<?php

namespace App\Filament\Resources\TrainingVideos\Schemas;

use App\Models\GeneralSetting;
use App\Models\TrainingVideo;
use App\Support\UploadLimits;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TrainingVideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Video Bilgileri')
                    ->description('Başlık, konu ve kimlerin görebileceği.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                                // Yayındaki videonun bağlantısı bozulmasın diye slug yalnızca oluştururken üretilir
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Sayfa adresinde kullanılır: /egitim-videolari/slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('topic')
                            ->label('Konu')
                            ->placeholder('ör. Talep Oluşturma, Yönetim Paneli')
                            ->helperText('Destek sayfasında videolar bu konulara göre filtrelenir.')
                            ->datalist(fn (): array => TrainingVideo::query()
                                ->whereNotNull('topic')
                                ->distinct()
                                ->orderBy('topic')
                                ->pluck('topic')
                                ->all())
                            ->maxLength(100),

                        Select::make('audience')
                            ->label('Kimler Görebilir')
                            ->options(TrainingVideo::AUDIENCES)
                            ->default('public')
                            ->required()
                            ->native(false),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->placeholder('Videoda anlatılan adımları kısaca yazın.')
                            ->rows(4)
                            ->maxLength(5000)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Sıra')
                            ->helperText('Küçük sayı önce gösterilir.')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Toggle::make('is_published')
                            ->label('Yayında')
                            ->helperText('Kapalıyken yalnızca yetkili kullanıcılar önizleyebilir.')
                            ->default(false)
                            ->inline(false),
                    ])->columns(2),

                Section::make('Video')
                    ->schema([
                        ToggleButtons::make('source_type')
                            ->label('Kaynak')
                            ->options(TrainingVideo::SOURCE_TYPES)
                            ->icons([
                                'upload' => 'heroicon-o-arrow-up-tray',
                                'url' => 'heroicon-o-link',
                            ])
                            ->default('upload')
                            ->required()
                            ->inline()
                            ->live()
                            ->columnSpanFull(),

                        FileUpload::make('video_path')
                            ->label('Video Dosyası')
                            ->disk(TrainingVideo::FILE_DISK)
                            ->directory(TrainingVideo::DIRECTORY)
                            ->visibility('private')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(fn (): int => UploadLimits::effectiveKb(GeneralSetting::query()->value('training_videos_max_upload_mb')))
                            ->helperText(fn (): string => 'MP4 (H.264) önerilir, WebM de desteklenir. En fazla '
                                .self::formatKb(UploadLimits::effectiveKb(GeneralSetting::query()->value('training_videos_max_upload_mb')))
                                .'. Sınırı Ayarlar / Eğitim Videoları sekmesinden değiştirebilirsiniz.')
                            ->previewable(false)
                            ->downloadable()
                            ->required(fn (Get $get): bool => $get('source_type') === 'upload')
                            ->visible(fn (Get $get): bool => $get('source_type') === 'upload')
                            ->columnSpanFull(),

                        TextInput::make('video_url')
                            ->label('Video Bağlantısı')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('YouTube ve Vimeo bağlantıları sayfaya gömülür; .mp4 gibi doğrudan video adresleri oynatıcıda açılır.')
                            ->url()
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('source_type') === 'url')
                            ->visible(fn (Get $get): bool => $get('source_type') === 'url')
                            ->columnSpanFull(),

                        FileUpload::make('poster_path')
                            ->label('Kapak Görseli')
                            ->helperText('İsteğe bağlı. Liste ve oynatıcıda video başlamadan önce gösterilir.')
                            ->image()
                            ->disk(TrainingVideo::POSTER_DISK)
                            ->directory(TrainingVideo::DIRECTORY.'/posters')
                            ->maxSize(5120),

                        FileUpload::make('subtitle_path')
                            ->label('Altyazı (.vtt)')
                            ->helperText('İsteğe bağlı. Türkçe WebVTT altyazı dosyası; işitme engelli kullanıcılar ve sessiz ortamlar için.')
                            ->disk(TrainingVideo::FILE_DISK)
                            ->directory(TrainingVideo::DIRECTORY.'/subtitles')
                            ->visibility('private')
                            ->acceptedFileTypes(['text/vtt', 'text/plain'])
                            ->maxSize(1024)
                            ->previewable(false)
                            ->visible(fn (Get $get): bool => $get('source_type') === 'upload'),
                    ])->columns(2),
            ]);
    }

    private static function formatKb(int $kb): string
    {
        return $kb >= 1024 ? floor($kb / 1024).' MB' : $kb.' KB';
    }
}
