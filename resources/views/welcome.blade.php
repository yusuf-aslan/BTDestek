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
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">BT</div>
                <h1 class="font-bold text-lg tracking-tight">Hastane <span class="text-blue-600">BT Destek</span></h1>
            </div>
            <nav class="hidden md:flex gap-6 text-sm font-medium">
                <a href="#submit" class="hover:text-blue-600 transition">Talep Oluştur</a>
                <a href="#track" class="hover:text-blue-600 transition">Sorgulama</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        
        <!-- Announcements -->
        @if($announcements->count() > 0)
            <div class="mb-8 space-y-3">
                @foreach($announcements as $announcement)
                    <div class="p-4 rounded-lg border flex gap-4 items-start {{ $announcement->type === 'danger' ? 'bg-red-50 border-red-200 text-red-800' : ($announcement->type === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-blue-50 border-blue-200 text-blue-800') }}">
                        <div class="mt-0.5">
                            @if($announcement->type === 'danger')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold">{{ $announcement->title }}</h3>
                            <p class="text-sm opacity-90">{{ $announcement->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Submit Form -->
            <div class="md:col-span-2 space-y-6" id="submit">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-xl font-bold">Yeni Destek Talebi</h2>
                        <p class="text-sm text-slate-500">Lütfen sorununuzu detaylı bir şekilde açıklayın.</p>
                    </div>
                    
                    @if(session('success'))
                        <div class="m-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('tickets.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-semibold">Adınız Soyadınız</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Örn: Dr. Ahmet Yılmaz">
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-semibold">Bölüm / Oda No</label>
                                <input type="text" name="department_room" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Örn: Dahiliye - Kat 2">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-semibold">Kategori</label>
                            <select name="category_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="">Seçiniz...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-semibold">Konu</label>
                            <input type="text" name="subject" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Kısa bir başlık">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-semibold">Sorun Detayı</label>
                            <textarea name="description" rows="4" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Lütfen sorunu açıklayın..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-blue-200">Talebi Gönder</button>
                    </form>
                </div>
            </div>

            <!-- Track Sidebar -->
            <div class="space-y-6">
                <div class="bg-blue-900 text-white rounded-xl p-6 shadow-xl" id="track">
                    <h2 class="text-xl font-bold mb-2">Talep Sorgula</h2>
                    <p class="text-blue-200 text-sm mb-4">Size verilen takip numarası ile talebinizin durumunu öğrenebilirsiniz.</p>
                    
                    <form action="{{ route('tickets.show') }}" method="GET" class="space-y-3">
                        <input type="text" name="tracking_number" required class="w-full px-3 py-2 bg-blue-800 border border-blue-700 rounded-lg text-white placeholder-blue-400 outline-none focus:border-white transition" placeholder="BT-2025-XXXXXX">
                        <button type="submit" class="w-full bg-white text-blue-900 font-bold py-2 rounded-lg hover:bg-blue-50 transition">Sorgula</button>
                    </form>

                    @error('tracking_number')
                        <p class="mt-2 text-red-300 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="font-bold mb-3">Hızlı Yardım</h3>
                    <ul class="text-sm space-y-2 text-slate-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Şifre Sıfırlama Kılavuzu
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Yazıcı Tanımlama
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            HBYS Giriş Sorunları
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <footer class="mt-12 py-8 border-t border-slate-200 text-center text-slate-500 text-sm">
        &copy; {{ date('Y') }} - Hastane Bilgi İşlem Birimi - Tüm Hakları Saklıdır.
    </footer>

</body>
</html>