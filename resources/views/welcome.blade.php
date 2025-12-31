@extends('layouts.public')

@section('content')
    <!-- Announcements Modal -->
    @if($announcements->count() > 0)
        <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-[100] flex items-center justify-center px-4" style="display: none;">
            <!-- Backdrop -->
            <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>

            <!-- Modal Panel -->
            <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                
                <!-- Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        Önemli Duyurular
                    </h3>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                    @foreach($announcements as $announcement)
                        <div class="p-4 rounded-xl border flex gap-4 items-start {{ $announcement->type === 'danger' ? 'bg-red-50 border-red-100 text-red-900' : ($announcement->type === 'warning' ? 'bg-amber-50 border-amber-100 text-amber-900' : 'bg-blue-50 border-blue-100 text-blue-900') }}">
                            <div class="shrink-0 mt-0.5">
                                @if($announcement->type === 'danger')
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-sm mb-1">{{ $announcement->title }}</h4>
                                <p class="text-sm opacity-90 leading-relaxed">{{ $announcement->content }}</p>
                                @if($announcement->published_at)
                                    <p class="text-xs opacity-75 mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $announcement->published_at->format('d.m.Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
                    <button @click="show = false" class="bg-slate-900 text-white font-bold py-2 px-6 rounded-lg text-sm hover:bg-slate-800 transition">
                        Okudum, Anladım
                    </button>
                </div>
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-10">
        
        <!-- Announcements Section -->
        @if($announcements->count() > 0)
            <div class="mb-10 space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    Güncel Duyurular
                </h2>
                <div class="grid gap-3">
                    @foreach($announcements as $announcement)
                        <div class="p-4 rounded-xl border flex gap-4 items-center transition {{ $announcement->type === 'danger' ? 'bg-red-50 border-red-100 text-red-900' : ($announcement->type === 'warning' ? 'bg-amber-50 border-amber-100 text-amber-900' : 'bg-blue-50 border-blue-100 text-blue-900') }}">
                            <div class="shrink-0">
                                @if($announcement->type === 'danger')
                                    <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
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
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 bg-slate-50/30">
                        <h2 class="text-2xl font-bold tracking-tight text-slate-800">Destek Talebi Oluştur</h2>
                        <p class="text-slate-500 mt-1">Hızlı çözüm için lütfen tüm alanları eksiksiz doldurun.</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="m-8 p-5 bg-emerald-50 border border-green-100 text-emerald-900 rounded-xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="font-medium text-sm leading-relaxed">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if($errors->has('error'))
                        <div class="m-8 p-5 bg-red-50 border border-red-100 text-red-900 rounded-xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center shrink-0">
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
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Adınız Soyadınız</label>
                                <input type="text" name="name" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50" placeholder="Örn: Dr. Ahmet Yılmaz">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Bölüm / Oda No</label>
                                <input type="text" name="department_room" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50" placeholder="Örn: Dahiliye - Kat 2">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Dahili No / Tel</label>
                                <input type="text" name="phone_number" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50" placeholder="Örn: 4455">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Kategori</label>
                            <select name="category_id" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50 appearance-none">
                                <option value="">Bir kategori seçin...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Konu Başlığı</label>
                            <input type="text" name="subject" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50" placeholder="Kısaca sorununuz nedir?">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Detaylı Açıklama</label>
                            <textarea name="description" rows="5" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition bg-slate-50/50" placeholder="Hata mesajı, cihaz markası vb. detayları belirtin..."></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Ekler (İsteğe Bağlı)</label>
                            <div x-data="{ files: [] }" class="relative border-2 border-dashed border-slate-200 rounded-xl p-4 hover:border-blue-400 transition bg-slate-50/50 text-center cursor-pointer group" @click="$refs.fileInput.click()">
                                <input x-ref="fileInput" type="file" name="attachments[]" multiple class="hidden" @change="files = Array.from($event.target.files)">
                                
                                <div x-show="files.length === 0" class="flex flex-col items-center gap-2 text-slate-500 group-hover:text-blue-500 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span class="text-sm font-medium">Dosyaları buraya sürükleyin veya seçin</span>
                                    <span class="text-xs text-slate-400 group-hover:text-blue-400">(Max 5MB / Resim, PDF, Log)</span>
                                </div>

                                <div x-show="files.length > 0" class="space-y-1" style="display: none;">
                                    <template x-for="file in files">
                                        <div class="flex items-center gap-2 text-sm text-slate-700 bg-white p-2 rounded-lg border border-slate-100">
                                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span x-text="file.name" class="truncate"></span>
                                            <span x-text="'(' + (file.size / 1024).toFixed(0) + ' KB)'" class="text-xs text-slate-400 ml-auto shrink-0"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition shadow-xl shadow-blue-100 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Talebi Gönder
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Knowledge Base Card -->
                @if($popularArticles->count() > 0)
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center justify-between">
                            <span class="flex items-center gap-2 text-sm uppercase tracking-wider">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Hızlı Çözümler
                            </span>
                            <a href="{{ route('kb.index') }}" class="text-[10px] text-blue-600 font-bold hover:underline">TÜMÜ</a>
                        </h3>
                        <div class="space-y-4">
                            @foreach($popularArticles as $article)
                                <a href="{{ route('kb.show', $article->slug) }}" class="block group">
                                    <h4 class="text-sm font-semibold text-slate-700 group-hover:text-blue-700 transition line-clamp-1">{{ $article->title }}</h4>
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $article->category->name }} • {{ $article->views }} Okunma</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Track Card -->
                <div class="bg-slate-900 text-white rounded-2xl p-8 shadow-2xl relative overflow-hidden group" id="track">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition"></div>
                    
                    <h2 class="text-xl font-bold mb-2">Talep Sorgula</h2>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Talebinizden sonra size verilen takip numarası ile güncel durumu kontrol edebilirsiniz.</p>
                    
                    <form action="{{ route('tickets.show') }}" method="GET" class="space-y-4">
                        <div class="relative">
                            <input type="text" name="tracking_number" required class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="BT-2025-XXXXXX">
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

                <!-- Info Card -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Önemli Hatırlatmalar
                    </h3>
                    <ul class="space-y-4">
                        @if($settings->important_reminders)
                            @foreach($settings->important_reminders as $reminder)
                                <li class="flex gap-3">
                                    <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 shrink-0"></div>
                                    <p class="text-xs text-slate-600 leading-relaxed">{!! $reminder['text'] !!}</p>
                                </li>
                            @endforeach
                        @else
                            <li class="flex gap-3">
                                <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 shrink-0"></div>
                                <p class="text-xs text-slate-600 leading-relaxed">Şu an bir hatırlatma bulunmuyor.</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>
    </main>
@endsection