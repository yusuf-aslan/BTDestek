<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\Category;
use App\Filament\Widgets\TicketStatsOverview;
use App\Filament\Pages\Reports\Widgets\TicketsChart;
use App\Filament\Pages\Reports\Widgets\TicketsPerCategoryChart;
use App\Filament\Pages\Reports\Widgets\TicketsPerDepartmentChart;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Raporlar ve İstatistikler';

    protected static ?string $navigationLabel = 'Raporlar';

    protected string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketStatsOverview::class,
            TicketsChart::class,
            TicketsPerCategoryChart::class,
            TicketsPerDepartmentChart::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Ticket::query())
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Talep Sahibi')
                    ->searchable(),
                TextColumn::make('category.department.name')
                    ->label('Bağlı Bölüm')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(30),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'yeni' => 'info',
                        'işlemde' => 'warning',
                        'beklemede' => 'gray',
                        'çözüldü' => 'success',
                        'iptal' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('resolver.name')
                    ->label('Çözen Personel')
                    ->placeholder('Henüz çözülmedi'),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Bölüm')
                    ->relationship('category.department', 'name'),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->multiple()
                    ->options(Category::pluck('name', 'id')),
                
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'yeni' => 'Yeni',
                        'işlemde' => 'İşlemde',
                        'beklemede' => 'Beklemede',
                        'çözüldü' => 'Çözüldü',
                        'iptal' => 'İptal',
                    ]),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Başlangıç'),
                        DatePicker::make('until')->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export')
                    ->label('Excel Olarak İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action('exportToExcel')
            ]);
    }

    public function exportToExcel()
    {
        return Excel::download(new TicketsExport($this->getFilteredTableQuery()), 'talep-raporu-' . now()->format('d-m-Y') . '.xlsx');
    }
}
