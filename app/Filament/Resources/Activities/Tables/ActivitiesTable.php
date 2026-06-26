<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use App\Exports\ActivitiesExport;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_date')
                    ->label('Faaliyet Tarihi')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Teknisyen')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->is_admin),
                TextColumn::make('activity_type')
                    ->label('Faaliyet Türü')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Süre')
                    ->numeric()
                    ->sortable()
                    ->suffix(' dk'),
                TextColumn::make('department')
                    ->label('Bölüm / Oda')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activity_type')
                    ->label('Faaliyet Türü')
                    ->options([
                        'Telefon Desteği' => 'Telefon Desteği',
                        'Saha Çalışması' => 'Saha Çalışması',
                        'Toplantı' => 'Toplantı',
                        'Rutin Bakım/Onarım' => 'Rutin Bakım/Onarım',
                        'Sistem/Altyapı Çalışması' => 'Sistem/Altyapı Çalışması',
                        'Diğer' => 'Diğer',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Teknisyen')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->is_admin),
                Filter::make('activity_date')
                    ->label('Tarih Aralığı')
                    ->form([
                        DatePicker::make('from')
                            ->label('Başlangıç Tarihi')
                            ->placeholder('GG.AA.YYYY'),
                        DatePicker::make('until')
                            ->label('Bitiş Tarihi')
                            ->placeholder('GG.AA.YYYY'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('activity_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('activity_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Başlangıç: ' . \Carbon\Carbon::parse($data['from'])->format('d.m.Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Bitiş: ' . \Carbon\Carbon::parse($data['until'])->format('d.m.Y');
                        }
                        return $indicators;
                    })
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->label('İşlemler')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
            ])
            ->recordAction(null)
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Excel Raporu İndir')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Table $table) {
                        $query = $table->getFilteredQuery();
                        return (new ActivitiesExport($query))->download('faaliyetler-' . now()->format('Y-m-d') . '.xlsx');
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('activity_date', 'desc');
    }
}
