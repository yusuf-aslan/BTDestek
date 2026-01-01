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
        
        return view('welcome', compact('announcements', 'categories', 'popularArticles'));
    }

    public function store(Request $request)
    {
        // Working Hours Check
        $settings = \Illuminate\Support\Facades\Cache::get('general_settings', \App\Models\GeneralSetting::first());
        
        if ($settings) {
            $now = now();
            $isWeekend = $now->isWeekend();
            $currentTime = $now->format('H:i:s');
            
            // Check Weekend
            if ($isWeekend && !$settings->weekend_tickets_allowed) {
                return redirect()->back()->withErrors(['error' => 'Hafta sonları sistem üzerinden talep kabul edilmemektedir. Acil durumlar için lütfen nöbetçi amirliği arayınız.'])->withInput();
            }

            // Check Work Hours
            if (!$settings->allow_tickets_outside_work_hours) {
                $start = \Carbon\Carbon::parse($settings->work_hours_start)->format('H:i:s');
                $end = \Carbon\Carbon::parse($settings->work_hours_end)->format('H:i:s');

                if ($currentTime < $start || $currentTime > $end) {
                    return redirect()->back()->withErrors(['error' => "Mesai saatleri ({$settings->work_hours_start} - {$settings->work_hours_end}) dışında sistem üzerinden talep kabul edilmemektedir."])->withInput();
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_room' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,txt,log|max:5120',
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
                $path = $file->store('attachments');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', "Talebiniz alındı. Takip Numaranız: {$ticket->tracking_number}");
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
