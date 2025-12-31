<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings->site_title)</title>
    @if($settings->site_favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings->site_favicon) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .clinical-shadow { shadow-[0_1px_3px_0_rgba(0,0,0,0.1),0_1px_2px_0_rgba(0,0,0,0.06)] }
        
        /* Knowledge Base Typography */
        .prose img { border-radius: 1rem; margin: 2rem auto; }
        .prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #1e293b; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #1e293b; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.75; color: #475569; }
        
        /* Prevent layout shift */
        html { overflow-y: scroll; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 selection:bg-blue-100 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shrink-0">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($settings->site_logo)
                    <img src="{{ asset('storage/' . $settings->site_logo) }}" alt="{{ $settings->site_title }}" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">{{ $settings->site_title }}</h1>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Bilgi İşlem Birimi</p>
                    </div>
                @endif
            </a>
            <nav class="hidden md:flex gap-8 text-sm font-semibold text-slate-500">
                <a href="{{ route('home') }}#submit" class="hover:text-blue-700 transition">Yeni Talep</a>
                <a href="{{ route('home') }}#track" class="hover:text-blue-700 transition">Durum Sorgula</a>
                <a href="{{ route('kb.index') }}" class="hover:text-blue-700 transition">Bilgi Bankası</a>
            </nav>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="mt-auto py-10 border-t border-slate-200 bg-white shrink-0">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">&copy; {{ date('Y') }} Hastane Bilgi İşlem Birimi</p>
            
            <div class="px-4 py-2 bg-slate-100 rounded-full border border-slate-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="text-xs font-bold text-slate-600 tracking-wider">IP Adresiniz: <span class="text-blue-700">{{ request()->ip() }}</span></span>
            </div>

            <div class="flex gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="/admin" class="hover:text-blue-600">Teknisyen Girişi</a>
            </div>
        </div>
    </footer>

    <!-- Image Lightbox Modal -->
    <div x-data="{ open: false, src: '' }" 
         @keydown.escape.window="open = false" 
         @open-lightbox.window="open = true; src = $event.detail"
         x-show="open" 
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="open = false" class="relative max-w-7xl max-h-[90vh] w-full flex items-center justify-center">
            <img :src="src" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl object-contain">
            <button @click="open = false" class="absolute -top-12 right-0 text-white hover:text-slate-300 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Find all images inside the article content area (prose class)
            const articleImages = document.querySelectorAll('.prose img');
            
            articleImages.forEach(img => {
                // Add styling to indicate clickability
                img.style.cursor = 'zoom-in';
                img.classList.add('hover:opacity-90', 'transition', 'duration-300');
                
                // Add click event
                img.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('open-lightbox', { 
                        detail: img.src 
                    }));
                });
            });
        });
    </script>
</body>
</html>