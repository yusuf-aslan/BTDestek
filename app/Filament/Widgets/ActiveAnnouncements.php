<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveAnnouncements extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Aktif Duyurular';

    public static function canView(): bool
    {
        return Announcement::where('is_active', true)->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Announcement::query()->where('is_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'danger' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('İçerik')
                    ->wrap(),
            ])
            ->paginated(false);
    }
}
