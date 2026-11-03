<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    private const MAX_WATCHED_TICKETS = 30;

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
            return redirect()->back()->withErrors(['error' => $intake->message()])->withInput();
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

    /**
     * Bu bilgisayardan açılan ve tarayıcının takip ettiği taleplerin güncel durumu.
     * Takip numarası zaten talebi sorgulamaya yettiği için bu uç yeni bir erişim açmaz.
     */
    public function updates(Request $request): JsonResponse
    {
        $numbers = collect(Arr::wrap($request->query('numbers')))
            ->filter(fn ($number) => is_string($number))
            ->take(self::MAX_WATCHED_TICKETS)
            ->values();

        $tickets = Ticket::query()
            ->where(fn ($query) => $query
                ->whereIn('tracking_number', $numbers)
                ->orWhere(fn ($query) => $query
                    ->where('ip_address', $request->ip())
                    ->where('updated_at', '>=', now()->subDays(7))))
            ->latest()
            ->limit(self::MAX_WATCHED_TICKETS * 2)
            ->get(['tracking_number', 'subject', 'status', 'resolution_note', 'updated_at'])
            ->map(function (Ticket $ticket) {
                $answered = TicketStatus::isClosed($ticket->status) || filled($ticket->resolution_note);

                return [
                    'tracking_number' => $ticket->tracking_number,
                    'subject' => $ticket->subject,
                    'signature' => md5($ticket->status.'|'.$ticket->resolution_note),
                    'heading' => $answered ? 'Talebiniz cevaplandı' : 'Talebinizin durumu değişti',
                    'status_label' => TicketStatus::labelFor($ticket->status),
                    'badge_classes' => TicketStatus::badgeClassesFor($ticket->status),
                    'note' => $this->noteExcerpt($ticket->resolution_note),
                    'note_html' => $ticket->resolution_note,
                    'updated_at' => $ticket->updated_at?->format('d.m.Y H:i'),
                    'url' => route('tickets.show', ['tracking_number' => $ticket->tracking_number]),
                ];
            });

        return response()
            ->json(['tickets' => $tickets])
            ->header('Cache-Control', 'no-store');
    }

    private function noteExcerpt(?string $note): ?string
    {
        if (blank($note)) {
            return null;
        }

        // Etiketler silinirken paragraflar birbirine yapışmasın
        $text = html_entity_decode(strip_tags(str_replace('<', ' <', $note)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text)), 140);
    }
}
