<x-mail::message>
# Merhaba {{ $ticket->name }},

#BT-{{ $ticket->tracking_number }} numaralı destek talebinizin durumu güncellendi.

**Yeni Durum:** {{ strtoupper($ticket->status) }}

<x-mail::button :url="route('home')">
Detayları Görüntüle
</x-mail::button>

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>