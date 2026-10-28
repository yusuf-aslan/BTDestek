<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('is_active', true)->latest()->get();
        $categories = Category::all();
        
        $popularArticles = \App\Models\Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('views', 'desc')
            ->take(3)
            ->get();

        $latestArticles = \App\Models\Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        $settings = \Illuminate\Support\Facades\Cache::rememberForever('general_settings', function () {
            return \App\Models\GeneralSetting::first();
        });

        // Get recent tickets from the current IP address in the last 24 hours
        $recentTickets = Ticket::where('ip_address', request()->ip())
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->get();
        
        return view('welcome', compact('announcements', 'categories', 'popularArticles', 'latestArticles', 'settings', 'recentTickets'));
    }

    public function store(Request $request)
    {
        // Mesai, hafta sonu ve tatil kuralları ana sayfadaki uyarıyla aynı yerden gelir
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('general_settings', function () {
            return \App\Models\GeneralSetting::first();
        });

        $intake = \App\Support\TicketIntake::at($settings);
        if ($intake->isClosed()) {
            return redirect()->back()->withErrors(['error' => $intake->closedReason])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'department_room' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:255',
            'broken_pc_ip' => 'nullable|ip',
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:100',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,txt,log|max:5120',
        ], [
            'subject.max' => 'Konu başlığı çok uzun. Lütfen kısa bir başlık yazıp detayları "Detaylı Açıklama" kısmına ekleyin (Maksimum 100 karakter).',
        ]);

        $trackingNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
        
        // Ensure uniqueness
        while (Ticket::where('tracking_number', $trackingNumber)->exists()) {
            $trackingNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
        }

        $ticket = Ticket::create(array_merge(
            collect($validated)->except('attachments')->toArray(),
            [
                'tracking_number' => $trackingNumber,
                'status' => 'yeni',
                'priority' => 'orta',
            ]
        ));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $successMessage = 'Talebiniz başarıyla alındı. Talep numaranızı ve aşağıdaki yazdırma seçeneğini kullanarak talebinizin bir kopyasını saklamanızı öneririz.';

        if ($settings?->isLunchBreakAt($ticket->created_at)) {
            $lunchBreakEnd = $settings->lunchBreakEndsAt($ticket->created_at)->format('H:i');
            $successMessage .= " Öğle arası nedeniyle talebiniz {$lunchBreakEnd} itibarıyla değerlendirilmeye başlanacaktır.";
        }

        return redirect()->back()->with('success_data', [
            'message' => $successMessage,
            'tracking_number' => $ticket->tracking_number,
            'ticket_id' => $ticket->id,
            'copy_data' => [
                'name' => $ticket->name,
                'email' => $ticket->email,
                'department_room' => $ticket->department_room,
                'phone_number' => $ticket->phone_number,
                'category_id' => $ticket->category_id,
            ]
        ]);
    }

    public function show(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $ticket = Ticket::where('tracking_number', $request->tracking_number)->first();

        if (!$ticket) {
            return redirect()->back()->withErrors(['tracking_number' => 'Geçersiz takip numarası.']);
        }

        return redirect()->route('home')->with('queried_ticket', $ticket);
    }
}
