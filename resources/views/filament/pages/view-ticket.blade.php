<x-filament-panels::page>
    <div wire:poll.15s="refreshLockStatus" class="space-y-6">
        @if($isEditLocked)
            <div class="rounded-xl border border-warning-300 bg-warning-50 p-4 text-warning-900 dark:border-warning-600/50 dark:bg-warning-950/40 dark:text-warning-100">
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-lock-closed" class="mt-0.5 h-5 w-5 shrink-0" />
                    <div>
                        <p class="font-semibold">Talep salt okunur görüntüleniyor</p>
                        <p class="mt-1 text-sm">
                            Bu talep şu anda {{ $lockOwnerName ?: 'başka bir kullanıcı' }} tarafından düzenleniyor.
                            İçeriği inceleyebilirsiniz ancak kilit bırakılana kadar değişiklik yapamazsınız.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-success-300 bg-success-50 p-4 text-success-900 dark:border-success-600/50 dark:bg-success-950/40 dark:text-success-100">
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-lock-open" class="mt-0.5 h-5 w-5 shrink-0" />
                    <div>
                        <p class="font-semibold">Talep düzenlemeye hazır</p>
                        <p class="mt-1 text-sm">Kilit bırakıldı. Üstteki “Düzenlemeye Geç” düğmesiyle talebi devralabilirsiniz.</p>
                    </div>
                </div>
            </div>
        @endif

        {{ $this->content }}
    </div>
</x-filament-panels::page>
