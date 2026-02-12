<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Filament\Imports\AssetImporter;
use App\Models\Asset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Cihaz Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'computer' => 'Bilgisayar',
                        'printer' => 'Yazıcı',
                        'network' => 'Ağ Cihazı',
                        'monitor' => 'Monitör',
                        'tablet' => 'Tablet',
                        'medical' => 'Tıbbi Cihaz',
                        'other' => 'Diğer',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'computer' => 'info',
                        'printer' => 'warning',
                        'network' => 'success',
                        'broken' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('model')
                    ->label('Marka Model')
                    ->searchable()
                    ->sortable()
                    ->tooltip(function (Asset $record): ?string {
                        $tooltipContent = [];
                        if (isset($record->specs['ram']) && $record->specs['ram']) {
                            $tooltipContent[] = 'RAM: ' . $record->specs['ram'];
                        }
                        if (isset($record->specs['monitor']) && $record->specs['monitor']) {
                            $tooltipContent[] = 'Monitör: ' . $record->specs['monitor'];
                        }

                        if (empty($tooltipContent)) {
                            return null; // No specs to show
                        }

                        return implode("\n", $tooltipContent);
                    }),
                TextColumn::make('location.anabirim')
                    ->label('Ana Birim')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('location.altbirim')
                    ->label('Alt Birim')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'retired' => 'Hurda',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'retired' => 'danger',
                        default => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Cihaz Türü')
                    ->options([
                        'computer' => 'Bilgisayar',
                        'printer' => 'Yazıcı',
                        'network' => 'Ağ Cihazı',
                        'monitor' => 'Monitör',
                    ]),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'retired' => 'Hurda',
                    ]),
                SelectFilter::make('location')
                    ->relationship('location', 'anabirim')
                    ->label('Ana Birim')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(AssetImporter::class)
                    ->label('Excel ile Envanter Yükle'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
