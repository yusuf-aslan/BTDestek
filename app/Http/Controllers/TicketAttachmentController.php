<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function download(TicketAttachment $attachment)
    {
        $user = auth()->user();
        $canView = $attachment->ticket_id
            ? Ticket::query()->visibleTo($user)->whereKey($attachment->ticket_id)->exists()
            : (bool) $user?->is_admin;

        abort_unless($canView, 403);

        if (! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}
