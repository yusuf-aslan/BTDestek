<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class TechnicianTicketsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected User $user,
        protected ?string $dateFrom,
        protected ?string $dateTo,
    ) {}

    public function title(): string
    {
        return 'Talepler';
    }

    public function query(): Builder
    {
        return Ticket::query()
            ->where('resolved_by', $this->user->id)
            ->where('status', 'çözüldü')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('resolved_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('resolved_at', '<=', $this->dateTo))
            ->with('category.department');
    }

    public function headings(): array
    {
        return [
            'Takip No',
            'Talep Sahibi',
            'Bölüm',
            'Kategori',
            'Konu',
            'Durum',
            'Çözüm Tarihi',
            'Çözüm Notu',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->tracking_number,
            $ticket->name,
            $ticket->category?->department?->name ?? '-',
            $ticket->category?->name ?? '-',
            $ticket->subject,
            $ticket->status,
            $ticket->resolved_at ? $ticket->resolved_at->format('d.m.Y H:i') : '-',
            $ticket->resolution_note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
