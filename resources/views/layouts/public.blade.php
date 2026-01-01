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
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
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
        
        @if($settings->is_dark_mode_enabled)
        .dark .prose h2, .dark .prose h3 { color: #f1f5f9; }
        .dark .prose p { color: #cbd5e1; }
        @endif

        /* Prevent layout shift */
        html { overflow-y: scroll; }
    </style>
    @if($settings->is_dark_mode_enabled)
    <script>
        // Set dark mode as early as possible to prevent flash
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @endif
</head>
<body class="bg-slate-50 @if($settings->is_dark_mode_enabled) dark:bg-slate-900 text-slate-900 dark:text-slate-100 selection:bg-blue-100 dark:selection:bg-blue-900 @else text-slate-900 selection:bg-blue-100 @endif min-h-screen flex flex-col transition-colors duration-300">

    <!-- Header -->
    <header class="bg-white @if($settings->is_dark_mode_enabled) dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 @else border-b border-slate-200 @endif sticky top-0 z-50 shrink-0 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($settings->site_logo)
                    <img src="{{ asset('storage/' . $settings->site_logo) }}" alt="{{ $settings->site_title }}" class="h-10 w-auto">
                @else
                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200 @if($settings->is_dark_mode_enabled) dark:shadow-blue-900/20 @endif">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight @if($settings->is_dark_mode_enabled) dark:text-white @endif">{{ $settings->site_title }}</h1>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Bilgi İşlem Birimi</p>
                    </div>
                @endif
            </a>
            
            <div class="flex items-center gap-8">
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600 @if($settings->is_dark_mode_enabled) dark:text-slate-300 @endif uppercase tracking-widest">
                    @foreach($public_menus as $menu)
                        @if($menu->children->count() > 0)
                            <!-- Dropdown Menu -->
                            <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                                <button class="flex items-center gap-1 hover:text-blue-700 @if($settings->is_dark_mode_enabled) dark:hover:text-blue-400 @endif transition py-2 border-b-2 border-transparent hover:border-blue-700 @if($settings->is_dark_mode_enabled) dark:hover:border-blue-400 @endif">
                                    {{ $menu->title }}
                                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute left-0 mt-0 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 py-2 z-[60]"
                                     style="display: none;">
                                    @foreach($menu->children as $child)
                                        <a href="{{ $child->url }}" target="{{ $child->target }}" class="block px-4 py-2 text-[11px] hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $menu->url }}" target="{{ $menu->target }}" class="hover:text-blue-700 @if($settings->is_dark_mode_enabled) dark:hover:text-blue-400 @endif transition py-2 border-b-2 border-transparent hover:border-blue-700 @if($settings->is_dark_mode_enabled) dark:hover:border-blue-400 @endif">
                                {{ $menu->title }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                @if($settings->is_dark_mode_enabled)
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
                @endif
            </div>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="mt-auto py-10 border-t border-slate-200 @if($settings->is_dark_mode_enabled) dark:border-slate-700 bg-white dark:bg-slate-800 @else bg-white @endif shrink-0 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 @if($settings->is_dark_mode_enabled) dark:text-slate-500 @endif text-xs font-medium uppercase tracking-widest">&copy; {{ date('Y') }} Hastane Bilgi İşlem Birimi</p>
            
            <div class="px-4 py-2 bg-slate-100 @if($settings->is_dark_mode_enabled) dark:bg-slate-700 rounded-full border border-slate-200 dark:border-slate-600 @else rounded-full border border-slate-200 @endif flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="text-xs font-bold text-slate-600 @if($settings->is_dark_mode_enabled) dark:text-slate-300 @endif tracking-wider">IP Adresiniz: <span class="text-blue-700 @if($settings->is_dark_mode_enabled) dark:text-blue-400 @endif">{{ request()->ip() }}</span></span>
            </div>

            <div class="flex gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="/admin" class="hover:text-blue-600 @if($settings->is_dark_mode_enabled) dark:hover:text-blue-400 @endif">Teknisyen Girişi</a>
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
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        const themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

            // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
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