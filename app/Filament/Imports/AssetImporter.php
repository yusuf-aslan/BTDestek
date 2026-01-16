<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Cihaz Adı / PC No')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('asset_tag')
                ->label('Demirbaş No / Envanter No')
                ->rules(['max:255']),

            ImportColumn::make('department')
                ->relationship(resolveUsing: 'name')
                ->label('Bölüm / Ana Birim'),

            ImportColumn::make('location')
                ->label('Konum / Alt Birim')
                ->rules(['max:255']),

            ImportColumn::make('model')
                ->label('Marka / Model'),

            ImportColumn::make('serial_number')
                ->label('Seri Numarası'),

            // Virtual columns mapped to 'specs' via model mutators
            ImportColumn::make('ram')
                ->label('RAM Kapasitesi'),

            ImportColumn::make('monitor')
                ->label('Monitör Modeli'),
        ];
    }

    public function resolveRecord(): Asset
    {
        // If 'asset_tag' is present in the import, try to find the record by it.
        // Otherwise, create a new one.
        
        if ($this->options['update_existing'] ?? false) {
             return Asset::firstOrNew([
                 'asset_tag' => $this->data['asset_tag'],
             ]);
        }

        return new Asset();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Varlık içe aktarma işlemi tamamlandı. ' . Number::format($import->successful_rows) . ' ' . str('satır')->plural($import->successful_rows) . ' başarıyla eklendi.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('satır')->plural($failedRowsCount) . ' aktarılamadı.';
        }

        return $body;
    }
}