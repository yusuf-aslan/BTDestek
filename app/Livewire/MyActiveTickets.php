<?php

namespace App\Livewire;

use AktifTalepler;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MyActiveTickets extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => AktifTalepler::query())
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
