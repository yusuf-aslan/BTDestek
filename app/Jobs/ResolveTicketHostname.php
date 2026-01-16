<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResolveTicketHostname implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->ticket->ip_address) {
            return;
        }

        try {
            // Attempt to resolve hostname
            // verify validity of IP
            if (filter_var($this->ticket->ip_address, FILTER_VALIDATE_IP)) {
                $hostname = gethostbyaddr($this->ticket->ip_address);

                // gethostbyaddr returns IP on failure
                if ($hostname && $hostname !== $this->ticket->ip_address) {
                    $this->ticket->computer_name = $hostname;
                    $this->ticket->saveQuietly(); // Prevent triggering updated observers
                }
            }
        } catch (\Exception $e) {
            // DNS resolution failed, ignore.
        }
    }
}