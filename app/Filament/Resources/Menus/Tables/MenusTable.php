<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('parent.title')
                    ->label('Üst Menü')
                    ->placeholder('Ana Menü')
                    ->sortable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->searchable(),

                TextColumn::make('target')
                    ->label('Hedef')
                    ->badge()
                    ->color(fn (string $state): string => $state === '_blank' ? 'warning' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === '_blank' ? 'Yeni Sekme' : 'Aynı Sekme'),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}