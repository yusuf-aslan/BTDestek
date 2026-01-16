<?php

namespace App\Observers;

use App\Mail\TicketCreated;
use App\Mail\TicketResolved;
use App\Mail\TicketStatusUpdated;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Dispatch Auto-Diagnostics
        \App\Jobs\ResolveTicketHostname::dispatch($ticket)->afterResponse();

        if ($ticket->email) {
            try {
                Mail::to($ticket->email)->send(new TicketCreated($ticket));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Illuminate\Support\Facades\Log::error("Mail sending failed for Ticket #{$ticket->tracking_number}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        if (!$ticket->email) {
            return;
        }

        // Check if status changed
        if ($ticket->isDirty('status')) {
            $newStatus = $ticket->status;
            
            try {
                if ($newStatus === 'çözüldü') {
                    Mail::to($ticket->email)->send(new TicketResolved($ticket));
                } else {
                    Mail::to($ticket->email)->send(new TicketStatusUpdated($ticket));
                }
            } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error("Mail sending failed for Ticket #{$ticket->tracking_number}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}