<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;

class TicketsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with(['category.department', 'resolver']);
    }

    public function headings(): array
    {
        return [
            'Takip No',
            'Talep Sahibi',
            'Bölüm/Oda',
            'Dahili No',
            'Bağlı Bölüm',
            'Kategori',
            'Konu',
            'Açıklama',
            'Durum',
            'Öncelik',
            'Çözen Personel',
            'Çözüm Notu',
            'Oluşturulma Tarihi',
            'Çözülme Tarihi',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->tracking_number,
            $ticket->name,
            $ticket->department_room,
            $ticket->phone_number,
            $ticket->category?->department?->name ?? '-',
            $ticket->category?->name,
            $ticket->subject,
            $ticket->description,
            ucfirst($ticket->status),
            ucfirst($ticket->priority),
            $ticket->resolver?->name ?? 'Henüz çözülmedi',
            $ticket->resolution_note,
            \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i'),
            $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->format('d.m.Y H:i') : '-',
        ];
    }
}