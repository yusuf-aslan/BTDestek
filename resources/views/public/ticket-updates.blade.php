@php
    $openedTickets = array_values(array_filter([
        session('success_data')['tracking_number'] ?? null,
        session('queried_ticket')?->tracking_number,
    ]));
@endphp

{{-- Talep güncelleme bildirimleri: resources/js/ticket-updates.js --}}
<div x-data="ticketUpdates(@js([
        'url' => route('tickets.updates'),
        'opened' => $openedTickets,
        'viewed' => session('queried_ticket')?->tracking_number,
    ]))"
    aria-live="polite"
    class="fixed bottom-4 left-4 z-[150] flex max-h-[calc(100vh-2rem)] w-[min(24rem,calc(100vw-2rem))] flex-col gap-3 overflow-y-auto sm:bottom-10 sm:left-10">
    <template x-for="update in pending" :key="update.number">
        <div x-transition.opacity class="rounded-2xl border-2 border-slate-200 border-l-[10px] border-l-blue-600 bg-white p-4 shadow-[0_20px_45px_-12px_rgba(0,0,0,0.35)] dark:border-slate-700 dark:border-l-blue-500 dark:bg-slate-800">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="update.heading"></p>
                    <p class="mt-0.5 font-mono text-[11px] text-slate-500 dark:text-slate-400" x-text="update.number"></p>
                </div>
                <button type="button" @click="dismiss(update.number)" title="Kapat" class="-mr-1 -mt-1 shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-slate-700 dark:hover:text-slate-200">
                    <span class="sr-only">Bildirimi kapat</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <p class="mt-2 truncate text-sm font-medium text-slate-700 dark:text-slate-200" :title="update.subject" x-text="update.subject"></p>
            <p x-show="update.note" class="mt-1 line-clamp-3 text-xs leading-relaxed text-slate-600 dark:text-slate-300" x-text="update.note"></p>

            <div class="mt-3 flex items-center justify-between gap-3">
                <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider" :class="update.badge" x-text="update.status"></span>
                <a :href="update.url" @click="dismiss(update.number)" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">Görüntüle</a>
            </div>
        </div>
    </template>
</div>
