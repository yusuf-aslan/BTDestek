<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use App\Models\Location;
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
                ->rules(['required', 'max:255'])
                ->guess(['PC_NO', 'PCNO']),

            ImportColumn::make('model')
                ->label('Marka / Model')
                ->guess(['PC_MODEL']),

            ImportColumn::make('ram')
                ->label('RAM Kapasitesi')
                ->guess(['RAM']),

            ImportColumn::make('monitor')
                ->label('Monitör Modeli')
                ->guess(['MONITOR']),
            
            // Virtual columns to capture location data
            ImportColumn::make('anabirim_raw')
                ->label('Ana Birim (Konum)')
                ->guess(['ANABIRIM']),

            ImportColumn::make('altbirim_raw')
                ->label('Alt Birim (Konum)')
                ->guess(['ALTBIRIM']),
        ];
    }

    public function resolveRecord(): ?Asset
    {
        // Find or create the location first, if location data is provided.
        $location = null;
        if ($this->data['anabirim_raw']) {
            $location = Location::firstOrCreate([
                'anabirim' => $this->data['anabirim_raw'],
                'altbirim' => $this->data['altbirim_raw'] ?? null,
            ]);
        }

        // Find existing asset or create a new instance.
        $asset = ($this->options['update_existing'] ?? false)
            ? Asset::firstOrNew(['name' => $this->data['name']])
            : new Asset();

        // Fill the main asset data from the columns.
        $asset->fill([
            'name' => $this->data['name'],
            'model' => $this->data['model'] ?? null,
        ]);
        
        // Use mutators to handle specs data.
        if (isset($this->data['ram'])) {
            $asset->ram = $this->data['ram'];
        }
        if (isset($this->data['monitor'])) {
            $asset->monitor = $this->data['monitor'];
        }

        // Associate the location if it exists.
        if ($location) {
            $asset->location()->associate($location);
        }

        // Save the record to the database.
        $asset->save();

        return $asset;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Envanter içe aktarma işlemi tamamlandı. ' . Number::format($import->successful_rows) . ' ' . str('satır')->plural($import->successful_rows) . ' başarıyla eklendi.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('satır')->plural($failedRowsCount) . ' aktarılamadı.';
        }

        return $body;
    }
}