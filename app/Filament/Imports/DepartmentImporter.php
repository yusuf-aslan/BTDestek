<?php

namespace App\Filament\Imports;

use App\Models\Department;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class DepartmentImporter extends Importer
{
    protected static ?string $model = Department::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Departman Adı')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->guessMapping(['name', 'NAME', 'DEPARTMAN', 'DEPARTMAN_ADI', 'ANABIRIM']),
        ];
    }

    public function resolveRecord(): ?Department
    {
        // Find department by name, or create a new one.
        return Department::firstOrCreate([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Departman içe aktarma işlemi tamamlandı. ' . Number::format($import->successful_rows) . ' ' . str('satır')->plural($import->successful_rows) . ' başarıyla eklendi.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('satır')->plural($failedRowsCount) . ' aktarılamadı.';
        }

        return $body;
    }
}
