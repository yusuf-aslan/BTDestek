@extends('layouts.public')

@section('content')
    <!-- Announcements Modal -->
    @if($announcements->count() > 0)
        @php 
            $latestId = $announcements->first()->id; 
        @endphp
        <div x-data="{ 
            show: {{ session('announcement_shown_' . $latestId) ? 'false' : 'true' }},
            close() {
                this.show = false;
                // Session focus handled via backend or just local state for this request
            }
        }" 
        x-show="show" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
            
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-lg rounded-3xl shadow-2xl shadow-slate-900/50 overflow-hidden"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4">
                        <button @click="close()" class="text-white/50 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold tracking-tight">Sistem Duyurusu</h2>
                            <p class="text-blue-100 text-xs font-medium uppercase tracking-widest mt-0.5 opacity-80">BT Destek Bilgilendirme</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-8 py-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="space-y-6">
                        @foreach($announcements as $announcement)
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-2 py-0.5 {{ $announcement->type === 'danger' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : ($announcement->type === 'warning' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300') }} text-[10px] font-bold rounded uppercase tracking-wider">
                                        {{ $announcement->type === 'danger' ? 'Kritik' : ($announcement->type === 'warning' ? 'Uyarı' : 'Bilgi') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $announcement->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 transition duration-300">{{ $announcement->title }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-2 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    {{ $announcement->content }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button @click="close()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-200 dark:shadow-none text-sm uppercase tracking-widest">
                        Anladım, Kapat
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Queried Ticket Success Modal (from Tracking) -->
    @if(session('queried_ticket'))
        @php $ticket = session('queried_ticket'); @endphp
        <div x-data="{ showStatus: true }" x-show="showStatus" class="fixed inset-0 z-[100] flex items-center justify-center px-4" style="display: none;">
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" @click="showStatus = false"></div>
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
                
                <!-- Modal Header -->
                <div class="bg-slate-900 dark:bg-slate-950 p-10 text-white relative">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-4 py-1.5 bg-blue-500/20 text-blue-400 text-[10px] font-bold uppercase tracking-[0.2em] rounded-full border border-blue-500/30">
                            Talep Durum Sorgulama
                        </span>
                        <div class="text-right">
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Takip Numarası</p>
                            <p class="text-xl font-black tracking-tighter text-blue-400">{{ $ticket->tracking_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-end justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight leading-none mb-2">Talebiniz <span class="text-blue-500">{{ Str::upper($ticket->status) }}</span></h2>
                            <p class="text-slate-400 text-sm font-medium">Son Güncelleme: {{ $ticket->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="shrink-0">
                            <div class="w-20 h-20 rounded-3xl bg-blue-600 flex items-center justify-center shadow-xl shadow-blue-900/40 rotate-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-10 max-h-[50vh] overflow-y-auto custom-scrollbar bg-white dark:bg-slate-800">
                    <div class="grid md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-slate-100 dark:border-slate-700">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Talep Sahibi</label>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $ticket->name }}</p>
                        </div>
                        <div class="space-y-1 text-right">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Öncelik</label>
                            <div class="flex items-center justify-end gap-2">
                                <span class="w-2 h-2 rounded-full {{ $ticket->priority === 'acil' ? 'bg-red-500 animate-pulse' : ($ticket->priority === 'yüksek' ? 'bg-orange-500' : 'bg-emerald-500') }}"></span>
                                <p class="font-bold text-slate-800 dark:text-white uppercase text-sm">{{ $ticket->priority }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-8">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Konu</label>
                        <p class="font-bold text-lg text-slate-800 dark:text-white leading-tight">{{ $ticket->subject }}</p>
                    </div>

                    <div class="space-y-2 mb-8">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Açıklama</label>
                        <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-2xl text-sm text-slate-600 dark:text-slate-300 leading-relaxed border border-slate-100 dark:border-slate-700">
                            {{ $ticket->description }}
                        </div>
                    </div>

                    @if($ticket->resolution_note)
                        <div class="space-y-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                            <label class="inline-flex items-center gap-2 px-3 py-1 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Teknisyen Çözüm Notu
                            </label>
                            <div class="p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-2xl text-blue-900 dark:text-blue-300 text-sm font-medium leading-relaxed shadow-sm shadow-blue-100">
                                {{ $ticket->resolution_note }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 dark:bg-slate-900/50 px-8 py-6 border-t border-slate-100 dark:border-slate-700 flex justify-center">
                    <button @click="showStatus = false" class="bg-slate-900 dark:bg-blue-600 text-white font-bold py-3 px-10 rounded-xl text-sm hover:bg-slate-800 dark:hover:bg-blue-700 transition shadow-xl shadow-slate-200">
                        Anladım
                    </button>
                </div>
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-10">
        
        <!-- Announcements Section -->
        @if($announcements->count() > 0)
            <div class="mb-10 space-y-4">
                <h2 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    Güncel Duyurular
                </h2>
                <div class="grid gap-3">
                    @foreach($announcements as $announcement)
                        <div class="p-4 rounded-xl border flex gap-4 items-center transition {{ $announcement->type === 'danger' ? 'bg-red-50 border-red-100 text-red-900 dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-300' : ($announcement->type === 'warning' ? 'bg-amber-50 border-amber-100 text-amber-900 dark:bg-amber-900/20 dark:border-amber-800/50 dark:text-amber-300' : 'bg-blue-50 border-blue-100 text-blue-900 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-300') }}">
                            <div class="shrink-0">
                                @if($announcement->type === 'danger')
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">{{ $announcement->title }}</h3>
                                <p class="text-xs opacity-80 mt-0.5">{{ $announcement->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-10">
            
            <!-- Submit Form -->
            <div class="lg:col-span-8 space-y-8" id="submit">
                @php
                    $isClosed = false;
                    $closedReason = '';
                    if ($settings) {
                        $now = now();
                        if ($now->isWeekend() && !$settings->weekend_tickets_allowed) {
                            $isClosed = true;
                            $closedReason = 'Hafta sonları sistem üzerinden talep kabul edilmemektedir. Acil durumlar için lütfen nöbetçi amirliği arayınız.';
                        } elseif (!$settings->allow_tickets_outside_work_hours) {
                            $currentTime = $now->format('H:i');
                            $start = \Carbon\Carbon::parse($settings->work_hours_start)->format('H:i');
                            $end = \Carbon\Carbon::parse($settings->work_hours_end)->format('H:i');
                            if ($currentTime < $start || $currentTime > $end) {
                                $isClosed = true;
                                $closedReason = "Sistemimiz günlük talep kabul saatleri dışındadır. Taleplerinizi hafta içi {$start} - {$end} saatleri arasında iletebilirsiniz.";
                            }
                        }
                    }
                @endphp

                @if($isClosed)
                    <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-100 dark:border-amber-800/50 p-6 rounded-2xl flex items-center gap-5 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-1">Şu Anda Talep Alınmamaktadır</h4>
                            <p class="font-medium text-sm text-amber-700 dark:text-amber-300/90 leading-relaxed">
                                {{ $closedReason }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors duration-300 {{ $isClosed ? 'opacity-75 grayscale-[0.5]' : '' }}">
                    @if(session('success_data'))
                        @php $successData = session('success_data'); @endphp
                        <div class="p-8 md:p-12 text-center animate-in fade-in zoom-in-95 duration-500 space-y-8">
                            <!-- Success Icon -->
                            <div class="mx-auto w-20 h-20 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center shadow-xl shadow-emerald-100/50 dark:shadow-none animate-bounce-short">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>

                            <!-- Title & Message -->
                            <div class="space-y-3">
                                <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Talebiniz Alındı!</h3>
                                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto text-sm leading-relaxed">
                                    {{ $successData['message'] }}
                                </p>
                            </div>

                            <!-- Tracking Number Box -->
                            <div class="max-w-md mx-auto p-6 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/60 rounded-3xl shadow-sm">
                                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-2">Talep Numaranız</span>
                                <span class="text-3xl font-black text-blue-600 dark:text-blue-400 tracking-wider bg-blue-50 dark:bg-blue-950 px-6 py-3 rounded-2xl inline-block border border-blue-100 dark:border-blue-900/30">
                                    {{ $successData['tracking_number'] }}
                                </span>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-3 font-semibold">Bu numara ile talebinizin durumunu dilediğiniz zaman sorgulayabilirsiniz.</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto pt-2">
                                <a href="{{ route('public.tickets.print', ['ticket' => $successData['ticket_id'], 'action' => 'print']) }}" target="_blank" class="w-full sm:flex-1 flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-extrabold py-4 px-6 rounded-2xl transition duration-300 text-sm shadow-lg shadow-slate-200 dark:shadow-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Yazdır
                                </a>
                                <button type="button" 
                                        onclick="copyTrackingNumber('{{ $successData['tracking_number'] }}')" 
                                        class="w-full sm:flex-1 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/60 text-slate-800 dark:text-white font-extrabold py-4 px-6 rounded-2xl transition duration-300 text-sm border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                    Talep No Kopyala
                                </button>
                            </div>

                            <!-- Reset / New Ticket Button -->
                            <div class="pt-8 border-t border-slate-100 dark:border-slate-700/60 max-w-md mx-auto">
                                <a href="{{ route('home') }}" class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 px-6 rounded-2xl transition duration-300 text-base uppercase tracking-[0.15em] shadow-lg shadow-emerald-100 dark:shadow-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Yeni Talep Ekle
                                </a>
                            </div>

                            <style>
                                @keyframes bounce-short {
                                    0%, 100% { transform: translateY(0); }
                                    50% { transform: translateY(-6px); }
                                }
                                .animate-bounce-short {
                                    animation: bounce-short 2s infinite ease-in-out;
                                }
                            </style>

                            <script>
                                function copyTrackingNumber(text) {
                                    if (!navigator.clipboard) {
                                        const el = document.createElement('textarea');
                                        el.value = text;
                                        document.body.appendChild(el);
                                        el.select();
                                        document.execCommand('copy');
                                        document.body.removeChild(el);
                                    } else {
                                        navigator.clipboard.writeText(text);
                                    }
                                    const btn = event.currentTarget;
                                    const originalText = btn.innerHTML;
                                    btn.innerHTML = 'Kopyalandı!';
                                    setTimeout(() => {
                                        btn.innerHTML = originalText;
                                    }, 2000);
                                }
                            </script>
                        </div>
                    @else
                        <div class="p-8 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/20">
                            <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">Destek Talebi Oluştur</h2>
                            <div class="mt-2 flex items-start gap-2 text-slate-500 dark:text-slate-400">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm leading-relaxed">
                                    Hızlı çözüm için lütfen tüm alanları eksiksiz doldurun. Talebiniz oluşturulduktan sonra size verilecek olan <strong class="text-slate-700 dark:text-slate-200">Talep Numarasını</strong> (Örn: BT-2026-XXXXXX) mutlaka not ediniz. Bu numara ile talebininizin durumunu dilediğiniz zaman sorgulayabilirsiniz.
                                </p>
                            </div>
                        </div>

                        @if($errors->has('error'))
                            <div class="mx-8 mt-6 mb-2 p-5 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 text-red-900 dark:text-red-300 rounded-xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="font-medium text-sm leading-relaxed">
                                    {{ $errors->first('error') }}
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                            @csrf
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Adınız Soyadınız</label>
                                    <input type="text" name="name" required {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Örn: Dr. Ahmet Yılmaz">
                                </div>
                                @if($settings->show_email_on_ticket_form && !empty($settings->mail_host) && !empty($settings->mail_port) && !empty($settings->mail_username) && !empty($settings->mail_password))
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">E-posta (İsteğe Bağlı)</label>
                                    <input type="email" name="email" {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="bildirim@ornek.com">
                                </div>
                                @endif
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Bölüm (İsteğe Bağlı)</label>
                                    <input type="text" name="department_room" {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Örn: Dahiliye - Kat 2">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Dahili No / Tel</label>
                                    <input type="text" name="phone_number" required {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Örn: 4455">
                                </div>
                                @if($settings->show_broken_pc_ip_on_ticket_form)
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1 whitespace-nowrap">Arızalı PC IP (Zorunlu Değil)</label>
                                    <input type="text" name="broken_pc_ip" {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Örn: 10.10.10.20">
                                </div>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Kategori</label>
                                <select name="category_id" required {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white appearance-none">
                                    <option value="">Bir kategori seçin...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Talep Başlığı (Kısaca)</label>
                                <input type="text" name="subject" required maxlength="100" {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Örn: Bilgisayar açılmıyor, Hasta taburcu edilemiyor vb.">
                                <p class="text-[10px] text-slate-400 ml-1 italic">* Lütfen kısa bir başlık yazın, detayları aşağıdaki kutucuğa ekleyin (Max. 100 Karakter).</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Detaylı Açıklama</label>
                                <textarea name="description" rows="5" required {{ $isClosed ? 'disabled' : '' }} class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-900/50 dark:text-white" placeholder="Yaşadığınız sorunu, varsa aldığınız hata mesajlarını ve cihazdaki belirtileri detaylıca buraya yazınız..."></textarea>
                            </div>

                            <div class="pt-4">
                                <button type="submit" @if($isClosed) disabled @endif 
                                        class="w-full {{ $isClosed ? 'bg-slate-400 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200 dark:shadow-none animate-pulse-subtle' }} text-white font-extrabold py-5 rounded-2xl transition-all duration-300 shadow-2xl flex items-center justify-center gap-4 text-base uppercase tracking-[0.2em]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    {{ $isClosed ? 'Şu An Talep Alınmıyor' : 'Talebi Gönder (Buraya Tıklayın)' }}
                                </button>
                            </div>

                            <div class="space-y-2 pt-6 border-t border-slate-100 dark:border-slate-800">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Ekler (İsteğe Bağlı)
                                </label>
                                <p class="text-[10px] text-slate-400 ml-1 italic mb-2">Talebinize dair fotoğraf veya belge eklemek isterseniz aşağıyı kullanabilirsiniz (Zorunlu değildir).</p>
                                <div x-data="{ 
                                    files: [], 
                                    dragging: false,
                                    handleDrop(e) {
                                        this.dragging = false;
                                        const droppedFiles = Array.from(e.dataTransfer.files);
                                        if (droppedFiles.length > 0) {
                                            this.files = [...this.files, ...droppedFiles];
                                            // Update the hidden file input
                                            const dataTransfer = new DataTransfer();
                                            this.files.forEach(file => dataTransfer.items.add(file));
                                            this.$refs.fileInput.files = dataTransfer.files;
                                        }
                                    }
                                }" 
                                class="relative border-2 border-dashed rounded-xl p-4 transition bg-slate-50/50 dark:bg-slate-900/50 text-center cursor-pointer group"
                                :class="dragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500'"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="handleDrop($event)"
                                @if(!$isClosed) @click="$refs.fileInput.click()" @endif>
                                    <input x-ref="fileInput" type="file" name="attachments[]" multiple class="hidden" @change="files = Array.from($event.target.files)" {{ $isClosed ? 'disabled' : '' }}>
                                    
                                    <div x-show="files.length === 0" class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400 group-hover:text-blue-500 transition">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <span class="text-sm font-medium">Dosyaları buraya sürükleyin veya seçin</span>
                                        <span class="text-xs text-slate-400 group-hover:text-blue-400">(Max 5MB / Resim, PDF, Log)</span>
                                    </div>

                                    <div x-show="files.length > 0" class="space-y-1" style="display: none;">
                                        <template x-for="file in files">
                                            <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span x-text="file.name" class="truncate"></span>
                                                <span x-text="'(' + (file.size / 1024).toFixed(0) + ' KB)'" class="text-xs text-slate-400 ml-auto shrink-0"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <style>
                                @keyframes pulse-subtle {
                                    0%, 100% { transform: scale(1); box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4); }
                                    50% { transform: scale(1.02); box-shadow: 0 20px 30px -5px rgba(16, 185, 129, 0.6); }
                                }
                                .animate-pulse-subtle:not(:disabled) {
                                    animation: pulse-subtle 3s infinite ease-in-out;
                                }
                            </style>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Recent Tickets from this IP -->
                @if(isset($recentTickets) && $recentTickets->count() > 0)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300 animate-in fade-in slide-in-from-top-4 duration-500">
                        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Son 24 Saatteki Talepleriniz
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Bu bilgisayardan son 24 saat içinde açılan talepleriniz aşağıda listelenmiştir. Detay için tıklayabilirsiniz.</p>
                        
                        <div class="space-y-3">
                            @foreach($recentTickets as $recentTicket)
                                <a href="{{ route('tickets.show', ['tracking_number' => $recentTicket->tracking_number]) }}" class="block p-3 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 hover:border-blue-200 dark:hover:border-blue-800 transition duration-200 group">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition truncate leading-snug">{{ $recentTicket->subject }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500">{{ $recentTicket->tracking_number }}</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500">•</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $recentTicket->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-0.5 text-[9px] font-bold rounded-full uppercase tracking-wider shrink-0
                                            {{ $recentTicket->status === 'yeni' ? 'bg-slate-100 text-slate-700 dark:bg-slate-900/60 dark:text-slate-300' : '' }}
                                            {{ $recentTicket->status === 'işlemde' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}
                                            {{ $recentTicket->status === 'beklemede' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                            {{ $recentTicket->status === 'çözüldü' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : '' }}
                                            {{ $recentTicket->status === 'iptal' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                        ">
                                            {{ $recentTicket->status }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Track Card -->
                <div class="bg-slate-900 dark:bg-slate-950 text-white rounded-2xl p-8 shadow-2xl relative overflow-hidden group" id="track">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition"></div>
                    
                    <h2 class="text-xl font-bold mb-2">Talep Sorgula</h2>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Talebinizden sonra size verilen takip numarası ile güncel durumu kontrol edebilirsiniz.</p>
                    
                    <form action="{{ route('tickets.show') }}" method="GET" class="space-y-4">
                        <div class="relative">
                            <input type="text" name="tracking_number" required class="w-full px-4 py-3 bg-slate-800/50 dark:bg-slate-900/50 border border-slate-700 dark:border-slate-800 rounded-xl text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="BT-2025-XXXXXX">
                        </div>
                        <button type="submit" class="w-full bg-white text-slate-900 font-bold py-3 rounded-xl hover:bg-blue-50 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Sorgula
                        </button>
                    </form>

                    @error('tracking_number')
                        <p class="mt-3 p-3 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-lg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Knowledge Base Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-6 flex items-center justify-between">
                        <span class="flex items-center gap-2 text-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Hızlı Çözümler
                        </span>
                        <a href="{{ route('kb.index') }}" class="text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline tracking-widest">TÜMÜ</a>
                    </h3>

                    <!-- Çok Okunanlar -->
                    <div class="mb-8">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <span class="w-1 h-3 bg-blue-500 rounded-full"></span>
                            Çok Okunanlar
                        </h4>
                        <div class="space-y-4">
                            @forelse($popularArticles as $article)
                                <a href="{{ route('kb.show', $article->slug) }}" class="block group">
                                    <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition line-clamp-1 leading-snug">{{ $article->title }}</h5>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded font-medium">{{ $article->category->name }}</span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $article->views }} Okunma</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-xs text-slate-400 italic">Henüz makale bulunmuyor.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- En Son Eklenenler -->
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <span class="w-1 h-3 bg-emerald-500 rounded-full"></span>
                            En Son Eklenenler
                        </h4>
                        <div class="space-y-4">
                            @forelse($latestArticles as $article)
                                <a href="{{ route('kb.show', $article->slug) }}" class="block group">
                                    <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition line-clamp-1 leading-snug">{{ $article->title }}</h5>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded font-medium">{{ $article->category->name }}</span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $article->published_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-xs text-slate-400 italic">Henüz makale bulunmuyor.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Önemli Hatırlatmalar
                    </h3>
                    <ul class="space-y-4">
                        @if($settings->important_reminders)
                            @foreach($settings->important_reminders as $reminder)
                                <li class="flex gap-3">
                                    <div class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full mt-1.5 shrink-0"></div>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{!! $reminder['text'] !!}</p>
                                </li>
                            @endforeach
                        @else
                            <li class="flex gap-3">
                                <div class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full mt-1.5 shrink-0"></div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Şu an bir hatırlatma bulunmuyor.</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>
    </main>
@endsection
