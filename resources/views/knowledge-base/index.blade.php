<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilgi Bankası - Hastane BT Destek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight">Hastane <span class="text-blue-700">BT Destek</span></h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Bilgi İşlem Birimi</p>
                </div>
            </a>
            <nav class="flex gap-8 text-sm font-semibold text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">Ana Sayfa</a>
                <a href="{{ route('home') }}#submit" class="hover:text-blue-700 transition">Yeni Talep</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800 mb-4">Size nasıl yardımcı olabiliriz?</h2>
            <form action="{{ route('kb.index') }}" method="GET" class="max-w-2xl mx-auto relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Sorununuzu arayın... (Örn: Parola sıfırlama, Yazıcı kurulumu)" 
                    class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition text-lg">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <div class="grid lg:grid-cols-12 gap-10">
            <!-- Sidebar -->
            <aside class="lg:col-span-3 space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Kategoriler</h3>
                    <div class="space-y-1">
                        <a href="{{ route('kb.index') }}" class="block px-4 py-2 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Tümü</a>
                        @foreach($categories as $category)
                            <a href="{{ route('kb.index', ['category' => $category->id]) }}" 
                                class="block px-4 py-2 rounded-lg text-sm font-medium {{ request('category') == $category->id ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Articles Grid -->
            <div class="lg:col-span-9">
                @if($articles->count() > 0)
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($articles as $article)
                            <a href="{{ route('kb.show', $article->slug) }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition group">
                                <div class="flex items-start justify-between mb-3">
                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider rounded-md">{{ $article->category->name }}</span>
                                    <span class="text-slate-400 text-xs flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        {{ $article->views }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-slate-800 group-hover:text-blue-700 transition mb-2">{{ $article->title }}</h3>
                                <p class="text-sm text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-12 text-center border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-1">Sonuç Bulunamadı</h3>
                        <p class="text-slate-500 text-sm">Aramanızla eşleşen bir makale bulunamadı. Lütfen farklı kelimeler deneyin.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="mt-20 py-10 border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4 text-center">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">&copy; {{ date('Y') }} Hastane Bilgi İşlem Birimi</p>
            <div class="flex gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Yardım Masası</a>
                <a href="#" class="hover:text-blue-600">KVKK Aydınlatma</a>
            </div>
        </div>
    </footer>

</body>
</html>
