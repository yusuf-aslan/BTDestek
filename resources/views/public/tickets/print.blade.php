<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep Çıktısı - {{ $ticket->tracking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-slate-100">
    <div class="max-w-4xl mx-auto p-8 bg-white my-10 rounded-lg shadow-md">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Destek Talebi Detayları</h1>
                <p class="text-slate-500">Oluşturulma Tarihi: {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-slate-600">Talep No</p>
                <p class="text-2xl font-bold text-blue-600 tracking-wider">{{ $ticket->tracking_number }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 border-t border-b border-slate-200 py-8">
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Talep Sahibi Bilgileri</h3>
                <p><span class="font-semibold text-slate-700">Ad Soyad:</span> {{ $ticket->name }}</p>
                @if($ticket->email)
                    <p><span class="font-semibold text-slate-700">E-posta:</span> {{ $ticket->email }}</p>
                @endif
                <p><span class="font-semibold text-slate-700">Bölüm / Oda:</span> {{ $ticket->department_room }}</p>
                <p><span class="font-semibold text-slate-700">Dahili / Tel:</span> {{ $ticket->phone_number }}</p>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Talep Detayları</h3>
                <p><span class="font-semibold text-slate-700">Kategori:</span> {{ $ticket->category->name }}</p>
                <p><span class="font-semibold text-slate-700">Durum:</span> <span class="capitalize font-medium px-2 py-1 rounded-full text-xs
                    {{ $ticket->status === 'yeni' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $ticket->status === 'işlemde' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $ticket->status === 'beklemede' ? 'bg-slate-200 text-slate-800' : '' }}
                    {{ $ticket->status === 'çözüldü' ? 'bg-emerald-100 text-emerald-800' : '' }}
                ">{{ $ticket->status }}</span></p>
                <p><span class="font-semibold text-slate-700">Öncelik:</span> <span class="capitalize">{{ $ticket->priority }}</span></p>
            </div>
        </div>

        <div class="py-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Konu</h3>
            <p class="text-xl font-bold text-slate-800">{{ $ticket->subject }}</p>
        </div>

        <div class="border-t border-slate-200 pt-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Açıklama</h3>
            <div class="prose max-w-none text-slate-700">
                {!! nl2br(e($ticket->description)) !!}
            </div>
        </div>

        @if($ticket->resolution_note)
            <div class="border-t border-slate-200 mt-8 pt-8">
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-2">Çözüm Notu</h3>
                <div class="prose max-w-none text-slate-700 bg-blue-50 p-4 rounded-lg">
                    {!! nl2br(e($ticket->resolution_note)) !!}
                </div>
            </div>
        @endif

    </div>

    @if($action !== 'pdf')
    <div class="max-w-4xl mx-auto px-8 mb-10 no-print">
        <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-center gap-4">
             <button onclick="window.print()" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Yazdır
            </button>
        </div>
    </div>
    @endif

    @if($action === 'print')
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
</body>
</html>
