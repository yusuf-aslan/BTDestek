<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Bilgi Bankası</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .prose img { border-radius: 1rem; margin: 2rem auto; }
        .prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #1e293b; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #1e293b; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.75; color: #475569; }
    </style>
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
                <a href="{{ route('kb.index') }}" class="hover:text-blue-700 transition">Bilgi Bankası</a>
                <a href="{{ route('home') }}#submit" class="hover:text-blue-700 transition font-bold text-blue-700">Yeni Talep</a>
            </nav>
        </div>
    </header>

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

            <div class="p-8 md:p-12 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h4 class="font-bold text-slate-800 mb-1">Bu makale yardımcı oldu mu?</h4>
                    <p class="text-sm text-slate-500">Geri bildiriminiz bizim için değerlidir.</p>
                </div>
                <div class="flex gap-3">
                    <button class="px-6 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-blue-50 hover:border-blue-200 transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.704a2 2 0 011.94 1.515l1.204 4.816A2 2 0 0119.912 19H19M14 10V4a2 2 0 00-2-2h-3a2 2 0 00-2 2v10m7 5H9"></path></svg>
                        Evet
                    </button>
                    <button class="px-6 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-red-50 hover:border-red-200 transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.296a2 2 0 01-1.94-1.515L2.152 7.669A2 2 0 014.088 5H4M10 14V20a2 2 0 002 2h3a2 2 0 002-2V10m-7 4H15"></path></svg>
                        Hayır
                    </button>
                </div>
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

    <footer class="mt-20 py-10 border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4 text-center">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">&copy; {{ date('Y') }} Hastane Bilgi İşlem Birimi</p>
            <div class="flex gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="{{ route('kb.index') }}" class="hover:text-blue-600">Bilgi Bankası</a>
                <a href="{{ route('home') }}" class="hover:text-blue-600">Yardım Masası</a>
            </div>
        </div>
    </footer>

</body>
</html>
