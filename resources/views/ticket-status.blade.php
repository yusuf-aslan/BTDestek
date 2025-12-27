<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Durumu - {{ $ticket->tracking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <header class="bg-white border-b border-slate-200">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-lg">BT</div>
                <h1 class="font-bold text-lg tracking-tight">BT Destek</h1>
            </a>
            <div class="text-sm font-medium text-slate-500">Talep Durum Sorgulama</div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">Takip No: {{ $ticket->tracking_number }}</h2>
                    <p class="text-sm text-slate-500">{{ $ticket->created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu.</p>
                </div>
                <div class="px-4 py-1.5 rounded-full font-bold text-sm uppercase tracking-wider {{ 
                    $ticket->status === 'yeni' ? 'bg-slate-100 text-slate-600' : (
                    $ticket->status === 'işlemde' ? 'bg-amber-100 text-amber-600' : (
                    $ticket->status === 'beklemede' ? 'bg-blue-100 text-blue-600' : (
                    $ticket->status === 'çözüldü' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600')))
                }}">
                    {{ $ticket->status }}
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase">Talep Sahibi</label>
                        <p class="font-medium text-slate-700">{{ $ticket->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase">Bölüm / Oda</label>
                        <p class="font-medium text-slate-700">{{ $ticket->department_room }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase">Kategori</label>
                        <p class="font-medium text-slate-700">{{ $ticket->category->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase">Öncelik</label>
                        <p class="font-medium text-slate-700">{{ $ticket->priority }}</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-400 uppercase block mb-1">Konu</label>
                    <p class="font-bold text-lg text-slate-800">{{ $ticket->subject }}</p>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg italic text-slate-600">
                    {{ $ticket->description }}
                </div>

                @if($ticket->resolution_note)
                    <div class="pt-6 border-t border-slate-100">
                        <label class="text-xs font-bold text-blue-600 uppercase block mb-2">Teknisyen Notu / Çözüm Açıklaması</label>
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg text-blue-800">
                            {{ $ticket->resolution_note }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center">
                <a href="{{ route('home') }}" class="text-blue-600 font-bold hover:underline">← Ana Sayfaya Dön</a>
            </div>
        </div>

    </main>

</body>
</html>