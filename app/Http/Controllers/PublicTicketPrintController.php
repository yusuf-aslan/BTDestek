<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicTicketPrintController extends Controller
{
    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['category']);

        $action = $request->get('action', 'print');

        if ($action === 'pdf') {
            $pdf = Pdf::loadView('public.tickets.print', ['ticket' => $ticket, 'action' => 'pdf']);
            return $pdf->stream('talep-' . $ticket->tracking_number . '.pdf');
        }

        return view('public.tickets.print', compact('ticket', 'action'));
    }
}
