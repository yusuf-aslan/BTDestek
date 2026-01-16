<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Filament\Imports\AssetImporter;
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
                TextColumn::make('asset_tag')
                    ->label('Demirbaş No')
                    ->searchable()
                    ->copyable(),
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
                TextColumn::make('department.name')
                    ->label('Bölüm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label('Zimmetli Kişi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'stock' => 'Depoda',
                        'maintenance' => 'Bakımda',
                        'retired' => 'Hurda',
                        'broken' => 'Arızalı',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'maintenance' => 'warning',
                        'broken', 'retired' => 'danger',
                        'stock' => 'gray',
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
                        'stock' => 'Depoda',
                        'maintenance' => 'Bakımda',
                        'broken' => 'Arızalı',
                        'retired' => 'Hurda',
                    ]),
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label('Bölüm')
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
                    ->label('Excel ile Varlık Yükle'),
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
