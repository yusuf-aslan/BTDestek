<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TechnicianReportExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected User $user,
        protected ?string $dateFrom,
        protected ?string $dateTo,
    ) {}

    public function sheets(): array
    {
        return [
            new TechnicianTicketsSheet($this->user, $this->dateFrom, $this->dateTo),
            new TechnicianActivitiesSheet($this->user, $this->dateFrom, $this->dateTo),
        ];
    }
}
