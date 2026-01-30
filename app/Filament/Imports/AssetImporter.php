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
                ->guessMapping(['PC_NO', 'PCNO']),

            ImportColumn::make('model')
                ->label('Marka / Model')
                ->guessMapping(['PC_MODEL']),

            ImportColumn::make('ram')
                ->label('RAM Kapasitesi')
                ->guessMapping(['RAM']),

            ImportColumn::make('monitor')
                ->label('Monitör Modeli')
                ->guessMapping(['MONITOR']),
            
            // Virtual columns to capture location data
            ImportColumn::make('anabirim_raw')
                ->label('Ana Birim (Konum)')
                ->guessMapping(['ANABIRIM']),

            ImportColumn::make('altbirim_raw')
                ->label('Alt Birim (Konum)')
                ->guessMapping(['ALTBIRIM']),
        ];
    }

    public function resolveRecord(): Asset
    {
        if ($this->options['update_existing'] ?? false) {
             return Asset::firstOrNew([
                 'name' => $this->data['name'],
             ]);
        }

        return new Asset();
    }

    public function afterSave(Asset $asset): void
    {
        if ($this->data['anabirim_raw']) {
            $location = Location::firstOrCreate([
                'anabirim' => $this->data['anabirim_raw'],
                'altbirim' => $this->data['altbirim_raw'] ?? null,
            ]);

            $asset->location()->associate($location)->save();
        }
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