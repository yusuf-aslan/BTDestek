<?php

namespace App\Filament\Resources\Holidays\Pages;

use App\Filament\Resources\Holidays\HolidayResource;
use App\Models\GeneralSetting;
use App\Models\Holiday;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    public function getSubheading(): ?string
    {
        $official = GeneralSetting::query()->value('official_holidays_enabled') ?? true;

        return $official
            ? 'Sabit tarihli resmi tatiller her yıl otomatik uygulanır. Buraya dini bayramları, arifeleri ve idari izinleri girin.'
            : 'Resmi tatiller Ayarlar / Mesai & Kurallar sekmesinden kapatılmış; yalnızca buraya girilen günler uygulanır.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('official')
                ->label('Resmi Tatiller')
                ->icon('heroicon-o-flag')
                ->color('gray')
                ->modalHeading('Otomatik Uygulanan Resmi Tatiller ('.now()->year.')')
                ->modalDescription('Aynı güne kayıt girerseniz (ör. arifenin kapanış saatini değiştirmek için) girdiğiniz kayıt geçerli olur.')
                // Panel CSS'i bu sayfaya özel sınıfları içermediği için düzen satır içi stille verilir
                ->modalContent(fn () => new HtmlString(
                    '<table style="width:100%;border-collapse:collapse;font-size:0.875rem">'
                    .collect(Holiday::officialFor(now()->year))->map(fn (array $holiday) => '<tr style="border-top:1px solid rgba(128,128,128,0.2)">'
                        .'<td style="padding:0.5rem 0">'.e($holiday['name'])
                        .(isset($holiday['closes_at']) ? ' <span style="opacity:0.6">(yarım gün, '.e($holiday['closes_at']).')</span>' : '').'</td>'
                        .'<td style="padding:0.5rem 0 0.5rem 1rem;text-align:right;white-space:nowrap;opacity:0.6">'.e($holiday['date']->translatedFormat('j F l')).'</td>'
                        .'</tr>')->implode('')
                    .'</table>'
                ))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat'),
            CreateAction::make(),
        ];
    }
}
