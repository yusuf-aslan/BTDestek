@extends('layouts.public')

@section('title', $video->title . ' - Eğitim Videoları')

@section('content')
    <main class="max-w-6xl mx-auto px-4 py-12">
        @include('training-videos.partials.preview-notice')

        <nav class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">
            <a href="{{ route('training.index') }}" class="hover:text-blue-600 transition">{{ $settings->training_videos_menu_title ?: 'EĞİTİM VİDEOLARI' }}</a>
            @if($video->category)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('training.index', ['modul' => $video->category->slug]) }}" class="hover:text-blue-600 transition">{{ $video->category->name }}</a>
            @endif
        </nav>

        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                <article class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="aspect-video bg-slate-900">
                        @if($embedUrl = $video->embedUrl())
                            <iframe src="{{ $embedUrl }}" title="{{ $video->title }}" class="w-full h-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @elseif($sourceUrl = $video->videoSourceUrl())
                            <video class="w-full h-full" controls preload="metadata" playsinline controlslist="nodownload"
                                @if($video->posterUrl()) poster="{{ $video->posterUrl() }}" @endif>
                                <source src="{{ $sourceUrl }}">
                                @if($video->subtitleUrl())
                                    <track kind="subtitles" src="{{ $video->subtitleUrl() }}" srclang="tr" label="Türkçe" default>
                                @endif
                                Tarayıcınız video oynatmayı desteklemiyor.
                            </video>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-2">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span class="text-sm">Bu video henüz hazır değil.</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @if(! $video->is_published)
                                <span class="px-2 py-0.5 bg-amber-400 text-amber-950 text-[10px] font-black uppercase tracking-wider rounded-md">Taslak</span>
                            @endif
                            @if($video->audience === 'staff')
                                <span class="px-2 py-0.5 bg-slate-800 text-white text-[10px] font-black uppercase tracking-wider rounded-md">Personel</span>
                            @endif
                            @if($video->category)
                                <a href="{{ route('training.index', ['modul' => $video->category->slug]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider rounded-md hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $video->category->dotColor() }}"></span>
                                    {{ $video->category->name }}
                                </a>
                            @endif
                        </div>

                        <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white mb-4">{{ $video->title }}</h2>

                        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500 dark:text-slate-400 mb-6">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $video->updated_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>{{ $video->views }} İzlenme</span>
                            </div>
                            @if($canManage)
                                <a href="{{ \App\Filament\Resources\TrainingVideos\TrainingVideoResource::getUrl('edit', ['record' => $video]) }}" class="ml-auto inline-flex items-center gap-1.5 text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Düzenle
                                </a>
                            @endif
                        </div>

                        @if($video->description)
                            <div class="text-slate-600 dark:text-slate-300 leading-relaxed">{!! nl2br(e($video->description)) !!}</div>
                        @endif
                    </div>
                </article>
            </div>

            <aside class="lg:col-span-4">
                @if($related->count() > 0)
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $video->category ? $video->category->name.' Modülündeki Diğer Videolar' : 'Diğer Videolar' }}</h3>
                    <div class="space-y-3">
                        @foreach($related as $item)
                            <a href="{{ route('training.show', $item) }}" class="flex gap-3 p-2 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 transition group">
                                <div class="relative w-28 shrink-0 aspect-video rounded-xl overflow-hidden bg-gradient-to-br from-blue-700 to-slate-800">
                                    @if($item->posterUrl())
                                        <img src="{{ $item->posterUrl() }}" alt="" loading="lazy" class="w-full h-full object-cover">
                                    @endif
                                    <span class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </span>
                                </div>
                                <div class="min-w-0 py-1">
                                    <p class="font-semibold text-sm text-slate-800 dark:text-slate-100 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition line-clamp-2">{{ $item->title }}</p>
                                    @if($item->category && ! $item->category->is($video->category))
                                        <p class="text-xs text-slate-400 mt-1 truncate">{{ $item->category->name }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 bg-blue-700 rounded-3xl p-6 text-white shadow-xl shadow-blue-200 dark:shadow-blue-900/20">
                    <h3 class="text-lg font-bold mb-1">Hala yardıma mı ihtiyacınız var?</h3>
                    <p class="text-blue-100 text-sm mb-4">Videoda aradığınızı bulamadıysanız destek talebi oluşturun.</p>
                    <a href="{{ route('home') }}#submit" class="inline-block px-5 py-2.5 bg-white text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-50 transition">
                        Destek Talebi Oluştur
                    </a>
                </div>
            </aside>
        </div>
    </main>
@endsection
