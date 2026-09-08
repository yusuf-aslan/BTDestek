<?php

namespace App\Http\Controllers;

use App\Services\TicketEditLock;
use Illuminate\Http\Request;

class TicketEditLockController extends Controller
{
    public function release(Request $request, int $ticket)
    {
        $data = $request->validate(['token' => ['required', 'uuid']]);
        app(TicketEditLock::class)->release($ticket, $data['token']);
        return response()->noContent();
    }
}
