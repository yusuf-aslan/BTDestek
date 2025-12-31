@extends('layouts.public')

@section('title', $article->title . ' - Bilgi Bankası')

@section('content')
    <main class="max-w-4xl mx-auto px-4 py-12">
        <nav class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">
            <a href="{{ route('kb.index') }}" class="hover:text-blue-600 transition">Bilgi Bankası</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-300">{{ $article->category->name }}</span>
        </nav>

        <article class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 md:p-12 border-b border-slate-100 bg-slate-50/30">
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-800 mb-6">{{ $article->title }}</h2>
                <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <span class="font-semibold">BT Destek Ekibi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ $article->published_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2 ml-auto">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>{{ $article->views }} Görüntülenme</span>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12 prose max-w-none">
                {!! $article->content !!}
            </div>
        </article>

        <div class="mt-12 bg-blue-700 rounded-3xl p-8 md:p-12 text-white shadow-2xl shadow-blue-200 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold mb-2">Hala yardıma mı ihtiyacınız var?</h3>
                <p class="text-blue-100">Makalede aradığınızı bulamadıysanız hemen bir destek talebi oluşturun.</p>
            </div>
            <a href="{{ route('home') }}#submit" class="px-8 py-4 bg-white text-blue-700 rounded-2xl font-bold hover:bg-blue-50 transition shadow-lg">
                Destek Talebi Oluştur
            </a>
        </div>
    </main>
@endsection