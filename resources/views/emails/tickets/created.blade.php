<x-mail::message>
# Merhaba {{ $ticket->name }},

Destek talebiniz başarıyla alınmıştır. En kısa sürede ilgili birim tarafından incelenecektir.

**Talep Bilgileri:**
- **Takip No:** {{ $ticket->tracking_number }}
- **Konu:** {{ $ticket->subject }}
- **Tarih:** {{ $ticket->created_at->format('d.m.Y H:i') }}

Durumunu sorgulamak için aşağıdaki butonu kullanabilirsiniz.

<x-mail::button :url="route('home', ['tracking_number' => $ticket->tracking_number])">
Talep Durumunu Sorgula
</x-mail::button>

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>