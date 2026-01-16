<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketPrintController extends Controller
{
    public function show(Ticket $ticket)
    {
        // Load relationships if needed
        $ticket->load(['category', 'asset', 'resolver']);

        return view('admin.tickets.print', compact('ticket'));
    }
}