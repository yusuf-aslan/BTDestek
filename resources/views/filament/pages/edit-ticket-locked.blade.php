<x-filament-panels::page>
    @if($hasEditLock)
        <div wire:poll.30s.keep-alive="heartbeatEditLock"
             x-data="{ release() { const body = new FormData(); body.append('_token', @js(csrf_token())); body.append('token', @js($editToken)); navigator.sendBeacon(@js(route('tickets.edit-lock.release', $this->record->id)), body); } }"
             x-on:pagehide.window="release()">
            {{ $this->content }}
        </div>
    @else
        <p>Düzenleme kilidi kullanılamıyor. Talep listesinden yeniden açabilirsiniz.</p>
        <x-filament::button tag="a" :href="$this->getResource()::getUrl('index')">Taleplere Dön</x-filament::button>
    @endif
</x-filament-panels::page>
