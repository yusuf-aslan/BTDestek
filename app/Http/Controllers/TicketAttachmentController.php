<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function download(TicketAttachment $attachment)
    {
        if (! Storage::exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::download($attachment->file_path, $attachment->file_name);
    }
}