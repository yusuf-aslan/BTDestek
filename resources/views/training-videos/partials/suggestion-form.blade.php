@php
    $suggestionErrors = $errors->getBag('suggestion');
    $suggestionSent = session('suggestion_sent');
    // Arama sonuçsuz kaldıysa form açık gelir ve aranan kelime konuya yazılır
    $openByDefault = $suggestionErrors->any() || $suggestionSent || ($search !== '' && $videos->total() === 0);
    $field = 'w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/30 focus:border-blue-500 outline-none transition';
    $label = 'block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5';
@endphp

<div id="video-oneri" class="mt-10 scroll-mt-24 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm"
    x-data="{ open: @js($openByDefault) }">
    <div class="p-5 md:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <span class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
        </span>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-slate-800 dark:text-white">Aradığınız videoyu bulamadınız mı?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Hangi konunun videosunu görmek istediğinizi yazın. En çok istenen konuları öncelikli hazırlıyoruz.</p>
        </div>
        <button type="button" x-show="!open" @click="open = true; $nextTick(() => $refs.topic?.focus())"
            class="shrink-0 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition">
            Konu öner
        </button>
    </div>

    <div x-show="open" @if(! $openByDefault) style="display: none" @endif class="border-t border-slate-100 dark:border-slate-700 p-5 md:p-6">
        @if($suggestionSent)
            <div class="flex items-start gap-3 p-4 mb-5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-800 dark:text-emerald-300">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ $suggestionSent }} Başka bir konu da önerebilirsiniz.</span>
            </div>
        @endif

        <form action="{{ route('training.suggest') }}" method="POST" class="grid sm:grid-cols-2 gap-4">
            @csrf
            {{-- Bot tuzağı: ekranda görünmez, dolu gelirse öneri kaydedilmez --}}
            <div class="hidden" aria-hidden="true">
                <label>Web sitesi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="sm:col-span-2">
                <label for="suggestion-topic" class="{{ $label }}">Konu <span class="text-red-500">*</span></label>
                <input id="suggestion-topic" x-ref="topic" type="text" name="topic" required minlength="5" maxlength="150"
                    value="{{ $suggestionSent ? '' : old('topic', $videos->total() === 0 ? $search : '') }}"
                    placeholder="Örn: Hasta kabulünde sevk bilgisi nasıl girilir?"
                    class="{{ $field }} {{ $suggestionErrors->has('topic') ? 'border-red-400' : '' }}">
                @if($suggestionErrors->has('topic'))
                    <p class="text-xs text-red-600 mt-1.5">{{ $suggestionErrors->first('topic') }}</p>
                @endif
            </div>

            <div>
                <label for="suggestion-module" class="{{ $label }}">Modül</label>
                <select id="suggestion-module" name="training_video_category_id" class="{{ $field }}">
                    <option value="">Emin değilim</option>
                    @foreach($suggestionCategories as $option)
                        <option value="{{ $option->id }}" @selected((string) old('training_video_category_id', $suggestionSent ? '' : $activeCategory?->id) === (string) $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="suggestion-department" class="{{ $label }}">Birim <span class="normal-case font-medium tracking-normal">(isteğe bağlı)</span></label>
                <input id="suggestion-department" type="text" name="department" maxlength="100" value="{{ $suggestionSent ? '' : old('department') }}"
                    placeholder="Örn: Acil Servis" class="{{ $field }}">
            </div>

            <div class="sm:col-span-2">
                <label for="suggestion-details" class="{{ $label }}">Açıklama <span class="normal-case font-medium tracking-normal">(isteğe bağlı)</span></label>
                <textarea id="suggestion-details" name="details" rows="3" maxlength="1000"
                    placeholder="Hangi ekranda, hangi adımda zorlanıyorsunuz?"
                    class="{{ $field }}">{{ $suggestionSent ? '' : old('details') }}</textarea>
                @if($suggestionErrors->has('details'))
                    <p class="text-xs text-red-600 mt-1.5">{{ $suggestionErrors->first('details') }}</p>
                @endif
            </div>

            <div>
                <label for="suggestion-name" class="{{ $label }}">Ad Soyad <span class="normal-case font-medium tracking-normal">(isteğe bağlı)</span></label>
                <input id="suggestion-name" type="text" name="name" maxlength="100" value="{{ $suggestionSent ? '' : old('name') }}" class="{{ $field }}">
            </div>

            <div class="flex items-end justify-end gap-3">
                <button type="button" @click="open = false" class="px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition">Vazgeç</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition">Öneriyi Gönder</button>
            </div>
        </form>
    </div>
</div>
