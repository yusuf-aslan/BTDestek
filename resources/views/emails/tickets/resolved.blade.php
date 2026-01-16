<x-mail::message>
# Merhaba {{ $ticket->name }},

#BT-{{ $ticket->tracking_number }} numaralı destek talebiniz **ÇÖZÜLDÜ** olarak işaretlenmiştir.

@if($ticket->resolution_note)
**Çözüm Notu:**
{{ $ticket->resolution_note }}
@endif

<x-mail::button :url="route('home')">
Talebi Görüntüle
</x-mail::button>

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>