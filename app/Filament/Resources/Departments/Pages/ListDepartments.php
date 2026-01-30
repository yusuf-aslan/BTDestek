<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Imports\DepartmentImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\Imports\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Excel\'den İçe Aktar')
                ->importer(DepartmentImporter::class),
            CreateAction::make(),
        ];
    }
}
