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
        
        return view('welcome', compact('announcements', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_room' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $trackingNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
        
        // Ensure uniqueness
        while (Ticket::where('tracking_number', $trackingNumber)->exists()) {
            $trackingNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
        }

        $ticket = Ticket::create(array_merge($validated, [
            'tracking_number' => $trackingNumber,
            'status' => 'yeni',
            'priority' => 'orta',
        ]));

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

        return view('ticket-status', compact('ticket'));
    }
}
