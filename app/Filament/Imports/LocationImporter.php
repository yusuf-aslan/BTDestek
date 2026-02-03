<?php

namespace App\Filament\Imports;

use App\Models\Location;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class LocationImporter extends Importer
{
    protected static ?string $model = Location::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('anabirim')
                ->label('Ana Birim')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->guess(['ANABIRIM']),
            ImportColumn::make('altbirim')
                ->label('Alt Birim')
                ->rules(['max:255'])
                ->guess(['ALTBIRIM']),
        ];
    }

    public function resolveRecord(): ?Location
    {
        // Don't create duplicates.
        return Location::firstOrCreate([
            'anabirim' => $this->data['anabirim'],
            'altbirim' => $this->data['altbirim'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Bölüm/Birim içe aktarma işlemi tamamlandı. ' . Number::format($import->successful_rows) . ' ' . str('satır')->plural($import->successful_rows) . ' başarıyla eklendi.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('satır')->plural($failedRowsCount) . ' aktarılamadı.';
        }

        return $body;
    }
}
