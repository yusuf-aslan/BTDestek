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
                ->rules(['required', 'max:255'])
                ->guessMapping(['PC_NO', 'PCNO']),

            ImportColumn::make('department')
                ->relationship(resolveUsing: 'name')
                ->label('Bölüm / Ana Birim')
                ->guessMapping(['ANABIRIM']),

            ImportColumn::make('location')
                ->label('Konum / Alt Birim')
                ->rules(['max:255'])
                ->guessMapping(['ALTBIRIM']),

            ImportColumn::make('model')
                ->label('Marka / Model')
                ->guessMapping(['PC_MODEL']),

            ImportColumn::make('ram')
                ->label('RAM Kapasitesi')
                ->guessMapping(['RAM']),

            ImportColumn::make('monitor')
                ->label('Monitör Modeli')
                ->guessMapping(['MONITOR']),
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

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Varlık içe aktarma işlemi tamamlandı. ' . Number::format($import->successful_rows) . ' ' . str('satır')->plural($import->successful_rows) . ' başarıyla eklendi.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('satır')->plural($failedRowsCount) . ' aktarılamadı.';
        }

        return $body;
    }
}