<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hastane BT Destek Portalı</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .clinical-shadow { shadow-[0_1px_3px_0_rgba(0,0,0,0.1),0_1px_2px_0_rgba(0,0,0,0.06)] }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 selection:bg-blue-100">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight">Hastane <span class="text-blue-700">BT Destek</span></h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Bilgi İşlem Birimi</p>
                </div>
            </div>
            <nav class="hidden md:flex gap-8 text-sm font-semibold text-slate-500">
                <a href="#submit" class="hover:text-blue-700 transition">Yeni Talep</a>
                <a href="#track" class="hover:text-blue-700 transition">Durum Sorgula</a>
                <a href="/admin" class="px-4 py-1.5 rounded-full border border-slate-200 hover:bg-slate-50 transition text-slate-600">Teknisyen Girişi</a>
            </nav>
        </div>
    </header>

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

                    <form action="{{ route('tickets.store') }}" method="POST" class="p-8 space-y-6">
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

                        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition shadow-xl shadow-blue-100 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Talebi Gönder
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                
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
                        <li class="flex gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs text-slate-600 leading-relaxed">Kritik tıbbi cihaz arızaları için sistem üzerinden talep oluşturduktan sonra dahili <b>1122</b>'yi arayınız.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs text-slate-600 leading-relaxed">Parola sıfırlama işlemleri sadece kimlik ibrazı ile şahsen yapılmaktadır.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 shrink-0"></div>
                            <p class="text-xs text-slate-600 leading-relaxed">Talebinize dair tüm güncellemeler otomatik olarak Bilgi İşlem ekranlarına düşmektedir.</p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <footer class="mt-20 py-10 border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">&copy; {{ date('Y') }} Hastane Bilgi İşlem Birimi</p>
            <div class="flex gap-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="#" class="hover:text-blue-600">Kullanım Koşulları</a>
                <a href="#" class="hover:text-blue-600">KVKK Aydınlatma</a>
            </div>
        </div>
    </footer>

</body>
</html>