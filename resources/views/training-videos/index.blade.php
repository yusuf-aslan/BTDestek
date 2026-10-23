@extends('layouts.public')

@php
    $pageTitle = $settings->training_videos_menu_title ?: 'EĞİTİM VİDEOLARI';
    $moduleLink = 'flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition shrink-0 whitespace-nowrap lg:whitespace-normal';
    $moduleIdle = 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60';
    $moduleActive = 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 font-semibold';
@endphp

@section('title', ($activeCategory ? $activeCategory->name.' - ' : '').'Eğitim Videoları - ' . $settings->site_title)

@section('content')
    <main class="w-full max-w-6xl mx-auto px-4 py-12">
        @include('training-videos.partials.preview-notice')

        <div class="text-center mb-10">
            <span class="inline-block text-[11px] font-black text-blue-700 dark:text-blue-400 uppercase tracking-[0.2em] mb-3">{{ $pageTitle }}</span>
            <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-3">Sistemi adım adım öğrenin</h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-8">
                {{ $settings->training_videos_description ?: 'Ekranların nasıl kullanıldığını anlatan kısa eğitim videoları.' }}
            </p>

            <form action="{{ route('training.index') }}" method="GET" class="max-w-2xl mx-auto relative">
                @if($activeCategory)
                    <input type="hidden" name="modul" value="{{ $activeCategory->slug }}">
                @endif
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ $activeCategory ? $activeCategory->name.' videolarında arayın...' : 'Video arayın... (Örn: Talep oluşturma, Stok girişi)' }}"
                    class="w-full pl-12 pr-4 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-50 dark:focus:ring-blue-900/30 focus:border-blue-500 outline-none transition text-lg dark:text-slate-100">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            {{-- Modül listesi: mobilde yatay kaydırılır, geniş ekranda sol sütunda sabit durur --}}
            <aside class="lg:col-span-3 lg:sticky lg:top-6 min-w-0">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-3">
                    <h3 class="px-3 pt-1 pb-2 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Modüller</h3>
                    <nav class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible lg:max-h-[70vh] lg:overflow-y-auto pb-1">
                        <a href="{{ route('training.index', array_filter(['search' => $search])) }}"
                            class="{{ $moduleLink }} {{ ! $activeCategory ? $moduleActive : $moduleIdle }}">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-slate-400"></span>
                            <span class="flex-1 truncate">Tüm Videolar</span>
                            <span class="text-xs text-slate-400 tabular-nums">{{ $totalCount }}</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('training.index', array_filter(['modul' => $category->slug, 'search' => $search])) }}"
                                class="{{ $moduleLink }} {{ $activeCategory?->is($category) ? $moduleActive : $moduleIdle }}">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $category->dotColor() }}"></span>
                                <span class="flex-1 truncate">{{ $category->name }}</span>
                                @if(! $category->is_active)
                                    <span class="text-[10px] font-bold uppercase text-amber-600">Pasif</span>
                                @endif
                                <span class="text-xs text-slate-400 tabular-nums">{{ $category->videos_count }}</span>
                            </a>
                        @endforeach
                    </nav>
                    @if($canManage)
                        <a href="{{ \App\Filament\Resources\TrainingVideoCategories\TrainingVideoCategoryResource::getUrl('index') }}" class="hidden lg:flex items-center gap-2 mt-2 px-3 py-2 border-t border-slate-100 dark:border-slate-700 text-xs font-semibold text-blue-700 dark:text-blue-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Modülleri yönet
                        </a>
                    @endif
                </div>
            </aside>

            <section class="lg:col-span-9 min-w-0">
                <div class="flex items-end justify-between gap-4 mb-5">
                    <div class="min-w-0">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2.5">
                            @if($activeCategory)
                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $activeCategory->dotColor() }}"></span>
                                {{ $activeCategory->name }}
                            @else
                                Tüm Videolar
                            @endif
                        </h3>
                        @if($activeCategory?->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $activeCategory->description }}</p>
                        @endif
                    </div>
                    <span class="text-sm text-slate-400 shrink-0">{{ $videos->total() }} video</span>
                </div>

                @if($videos->count() > 0)
                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($videos as $video)
                            <a href="{{ route('training.show', $video) }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-500 transition group overflow-hidden flex flex-col">
                                <div class="relative aspect-video bg-gradient-to-br from-blue-700 to-slate-800 overflow-hidden">
                                    @if($video->posterUrl())
                                        <img src="{{ $video->posterUrl() }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="w-14 h-14 rounded-full bg-white/90 text-blue-700 flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                                            <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    </div>
                                    <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                        @if(! $video->is_published)
                                            <span class="px-2 py-0.5 bg-amber-400 text-amber-950 text-[10px] font-black uppercase tracking-wider rounded-md">Taslak</span>
                                        @endif
                                        @if($video->audience === 'staff')
                                            <span class="px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-black uppercase tracking-wider rounded-md">Personel</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-5 flex-1 flex flex-col">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        @if($video->category)
                                            <span class="inline-flex items-center gap-1.5 min-w-0 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $video->category->dotColor() }}"></span>
                                                <span class="truncate">{{ $video->category->name }}</span>
                                            </span>
                                        @else
                                            <span></span>
                                        @endif
                                        <span class="text-slate-400 text-xs flex items-center gap-1 shrink-0">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            {{ $video->views }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition mb-1">{{ $video->title }}</h3>
                                    @if($video->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ Str::limit($video->description, 120) }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $videos->links() }}
                    </div>
                @else
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-dashed border-slate-200 dark:border-slate-700">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        @if($search !== '')
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-1">Sonuç Bulunamadı</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Aramanızla eşleşen bir video bulunamadı. Lütfen farklı kelimeler deneyin.</p>
                        @elseif($activeCategory)
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-1">Bu modülde henüz video yok</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Videolar hazırlandıkça burada yayınlanacak.</p>
                        @else
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 mb-1">Henüz video eklenmedi</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Eğitim videoları hazırlandıkça burada yayınlanacak.</p>
                        @endif
                        @if($canManage && $search === '')
                            <a href="{{ \App\Filament\Resources\TrainingVideos\TrainingVideoResource::getUrl('create') }}" class="inline-block mt-5 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition">Video ekle</a>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </main>
@endsection
