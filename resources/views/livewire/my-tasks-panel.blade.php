<div class="tk-panel" wire:poll.60s>
    <style>
        .tk-panel { border-radius: 16px; overflow: hidden; border: 1px solid rgba(156,163,175,.15); background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 16px; }
        .dark .tk-panel { background: #1e2535; border-color: rgba(255,255,255,.08); }
        .tk-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 14px 20px; border-bottom: 1px solid rgba(156,163,175,.12); background: rgba(99,102,241,.04); }
        .dark .tk-head { background: rgba(99,102,241,.08); border-color: rgba(255,255,255,.06); }
        .tk-title { font-size: 14px; font-weight: 700; color: #374151; }
        .dark .tk-title { color: #e5e7eb; }
        .tk-chip { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; }
        .tk-chip-late { background: rgba(239,68,68,.12); color: #dc2626; }
        .tk-chip-today { background: rgba(245,158,11,.14); color: #b45309; }
        .tk-chip-open { background: rgba(99,102,241,.12); color: #4f46e5; }
        .dark .tk-chip-late { background: rgba(239,68,68,.22); color: #fca5a5; }
        .dark .tk-chip-today { background: rgba(245,158,11,.22); color: #fcd34d; }
        .dark .tk-chip-open { background: rgba(99,102,241,.22); color: #c7d2fe; }
        .tk-head-links { margin-left: auto; display: flex; gap: 14px; font-size: 12px; font-weight: 700; }
        .tk-link { color: #4f46e5; text-decoration: none; }
        .tk-link:hover { text-decoration: underline; }
        .dark .tk-link { color: #a5b4fc; }

        .tk-add { display: flex; gap: 8px; padding: 12px 20px; border-bottom: 1px solid rgba(156,163,175,.10); }
        .dark .tk-add { border-color: rgba(255,255,255,.05); }
        .tk-input, .tk-select { border-radius: 9px; border: 1px solid rgba(156,163,175,.30); background: #fff; color: #111827; font-size: 13px; padding: 7px 11px; outline: none; }
        .tk-input { flex: 1; min-width: 0; }
        .tk-input:focus, .tk-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
        .dark .tk-input, .dark .tk-select { background: #171d2a; color: #e5e7eb; border-color: rgba(255,255,255,.12); }
        .tk-btn { border: 0; border-radius: 9px; padding: 7px 14px; font-size: 13px; font-weight: 700; color: #fff; background: #6366f1; cursor: pointer; white-space: nowrap; }
        .tk-btn:hover { background: #4f46e5; }
        .tk-error { padding: 0 20px 8px; font-size: 12px; color: #dc2626; }
        @media (max-width: 640px) { .tk-add { flex-wrap: wrap; } .tk-input { flex-basis: 100%; } }

        .tk-row { display: flex; align-items: center; gap: 12px; padding: 10px 20px; border-bottom: 1px solid rgba(156,163,175,.08); }
        .tk-row:last-child { border-bottom: 0; }
        .tk-row:hover { background: rgba(99,102,241,.05); }
        .tk-check { flex-shrink: 0; width: 22px; height: 22px; border-radius: 7px; border: 2px solid rgba(156,163,175,.6); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: transparent; transition: all .15s; }
        .tk-check:hover { border-color: #10b981; color: #10b981; background: rgba(16,185,129,.08); }
        .tk-main { flex: 1; min-width: 0; }
        .tk-name { display: block; font-size: 13px; font-weight: 600; color: #374151; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .tk-name:hover { color: #4f46e5; }
        .dark .tk-name { color: #d1d5db; }
        .tk-meta { display: flex; flex-wrap: wrap; gap: 2px 10px; margin-top: 2px; font-size: 11px; color: #6b7280; }
        .dark .tk-meta { color: #9ca3af; }
        .tk-due { flex-shrink: 0; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 99px; white-space: nowrap; background: rgba(59,130,246,.10); color: #2563eb; }
        .tk-due-late { background: rgba(239,68,68,.12); color: #dc2626; }
        .tk-due-today { background: rgba(245,158,11,.14); color: #b45309; }
        .tk-due-none { background: rgba(107,114,128,.10); color: #6b7280; }
        .dark .tk-due { background: rgba(59,130,246,.20); color: #93c5fd; }
        .dark .tk-due-late { background: rgba(239,68,68,.22); color: #fca5a5; }
        .dark .tk-due-today { background: rgba(245,158,11,.22); color: #fcd34d; }
        .dark .tk-due-none { background: rgba(107,114,128,.20); color: #d1d5db; }
        .tk-prio { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .tk-prio-acil { background: #ef4444; } .tk-prio-yüksek { background: #f59e0b; } .tk-prio-orta { background: #3b82f6; } .tk-prio-düşük { background: #9ca3af; }
        .tk-empty { padding: 22px 20px; text-align: center; font-size: 13px; color: #9ca3af; }
    </style>

    <div class="tk-head">
        <span>📝</span>
        <span class="tk-title">İş Listem</span>
        @if($overdueCount)
            <span class="tk-chip tk-chip-late">{{ $overdueCount }} gecikmiş</span>
        @endif
        @if($todayCount)
            <span class="tk-chip tk-chip-today">{{ $todayCount }} bugün</span>
        @endif
        @if($openCount)
            <span class="tk-chip tk-chip-open">{{ $openCount }} açık</span>
        @endif
        <div class="tk-head-links">
            <a href="{{ $createUrl }}" class="tk-link">+ Ayrıntılı ekle</a>
            <a href="{{ $listUrl }}" class="tk-link">Tümünü gör →</a>
        </div>
    </div>

    <form wire:submit="add" class="tk-add">
        <input type="text" wire:model="title" class="tk-input" placeholder="Hızlı iş ekle… (örn. Acil serviste yazıcı kurulumu)" maxlength="200" aria-label="Yeni iş">
        <select wire:model="due" class="tk-select" aria-label="Son tarih">
            <option value="today">Bugün</option>
            <option value="tomorrow">Yarın</option>
            <option value="week">Bu hafta</option>
            <option value="none">Süresiz</option>
        </select>
        <button type="submit" class="tk-btn" wire:loading.attr="disabled" wire:target="add">Ekle</button>
    </form>
    @error('title') <div class="tk-error">{{ $message }}</div> @enderror

    @forelse($tasks as $task)
        @php
            $dueClass = match (true) {
                ! $task->due_at => 'tk-due-none',
                $task->isOverdue() => 'tk-due-late',
                $task->isDueToday() => 'tk-due-today',
                default => '',
            };
            $progress = $task->checklistProgress();
        @endphp
        <div class="tk-row" wire:key="task-{{ $task->id }}">
            <button type="button" class="tk-check" wire:click="complete({{ $task->id }})" wire:loading.attr="disabled" title="Tamamlandı olarak işaretle" aria-label="{{ $task->title }} tamamlandı">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
            </button>
            <span class="tk-prio tk-prio-{{ $task->priority }}" title="Öncelik: {{ \App\Models\Task::PRIORITIES[$task->priority] ?? $task->priority }}"></span>
            <div class="tk-main">
                <a href="{{ \App\Filament\Resources\Tasks\TaskResource::getUrl('edit', ['record' => $task]) }}" class="tk-name" title="{{ $task->title }}">{{ $task->title }}</a>
                @if($progress || $task->ticket || $task->repeat || $task->status === 'devam')
                    <div class="tk-meta">
                        @if($task->status === 'devam')<span>▶ Devam ediyor</span>@endif
                        @if($progress)<span>✓ {{ $progress['done'] }}/{{ $progress['total'] }} adım</span>@endif
                        @if($task->ticket)<span>Talep {{ $task->ticket->tracking_number }}</span>@endif
                        @if($task->repeat)<span>↻ {{ \App\Models\Task::REPEATS[$task->repeat] ?? '' }}</span>@endif
                    </div>
                @endif
            </div>
            <span class="tk-due {{ $dueClass }}" title="{{ $task->due_at?->translatedFormat('j F Y H:i') }}">{{ $task->dueLabel() ?? 'Süresiz' }}</span>
        </div>
    @empty
        <div class="tk-empty">Açık işiniz yok. Yukarıdan hızlıca ekleyebilirsiniz.</div>
    @endforelse

    @if($openCount > $tasks->count())
        <a href="{{ $listUrl }}" class="tk-link" style="display:block;padding:10px 20px;text-align:center;border-top:1px solid rgba(156,163,175,.12);font-size:12px;font-weight:700;">{{ $openCount - $tasks->count() }} iş daha →</a>
    @endif
</div>
