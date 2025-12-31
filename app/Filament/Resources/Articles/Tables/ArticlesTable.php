<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                
                IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('views')
                    ->label('Görüntülenme')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Yayınlanma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                
                SelectFilter::make('is_published')
                    ->label('Durum')
                    ->options([
                        '1' => 'Yayınlanmış',
                        '0' => 'Taslak',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}