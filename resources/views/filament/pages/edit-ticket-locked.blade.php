<x-filament-panels::page>
    @if($hasEditLock)
        <div x-data="{
                idleTimer: null,
                heartbeatTimer: null,
                released: false,
                initLock() {
                    this.resetIdleTimer();
                    this.heartbeatTimer = setInterval(() => {
                        if (! this.released) {
                            this.$wire.heartbeatEditLock();
                        }
                    }, 25000);
                },
                recordActivity() {
                    if (! this.released) {
                        this.resetIdleTimer();
                    }
                },
                resetIdleTimer() {
                    clearTimeout(this.idleTimer);
                    this.idleTimer = setTimeout(() => this.releaseForInactivity(), 120000);
                },
                async releaseForInactivity() {
                    if (this.released) return;
                    this.released = true;
                    clearInterval(this.heartbeatTimer);
                    await this.$wire.releaseIdleTicket();
                },
                release() {
                    if (this.released) return;
                    this.released = true;
                    clearTimeout(this.idleTimer);
                    clearInterval(this.heartbeatTimer);
                    const body = new FormData();
                    body.append('_token', @js(csrf_token()));
                    body.append('token', @js($editToken));
                    navigator.sendBeacon(@js(route('tickets.edit-lock.release', $this->record->id)), body);
                }
             }"
             x-init="initLock()"
             x-on:pointerdown.window="recordActivity()"
             x-on:keydown.window="recordActivity()"
             x-on:input.window="recordActivity()"
             x-on:change.window="recordActivity()"
             x-on:paste.window="recordActivity()"
             x-on:pagehide.window="release()">
            {{ $this->content }}
        </div>
    @else
        <p>Düzenleme kilidi kullanılamıyor. Talep listesinden yeniden açabilirsiniz.</p>
        <x-filament::button tag="a" :href="$this->getResource()::getUrl('index')">Taleplere Dön</x-filament::button>
    @endif
</x-filament-panels::page>
