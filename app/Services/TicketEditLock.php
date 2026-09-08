<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketEditLock
{
    public function acquire(int $id, string $token): void
    {
        DB::transaction(function () use ($id, $token) {
            $ticket = Ticket::query()->visibleTo(auth()->user())->whereKey($id)->lockForUpdate()->firstOrFail();
            $this->assertAvailable($ticket, $token);
            DB::table('tickets')->where('id', $id)->update([
                'edit_lock_user_id' => auth()->id(),
                'edit_lock_token' => $token,
                'edit_lock_expires_at' => now()->addMinutes(2),
            ]);
        });
    }

    public function release(int $id, string $token): void
    {
        DB::table('tickets')->where('id', $id)
            ->where('edit_lock_user_id', auth()->id())->where('edit_lock_token', $token)
            ->update(['edit_lock_user_id' => null, 'edit_lock_token' => null, 'edit_lock_expires_at' => null]);
    }

    public function run(int $id, ?string $token, callable $callback): mixed
    {
        return DB::transaction(function () use ($id, $token, $callback) {
            $ticket = Ticket::query()->visibleTo(auth()->user())->whereKey($id)->lockForUpdate()->firstOrFail();
            $this->assertAvailable($ticket, $token);
            if ($token !== null && ($ticket->edit_lock_token !== $token || $ticket->edit_lock_expires_at <= now()->toDateTimeString())) {
                throw ValidationException::withMessages(['edit_lock' => 'Düzenleme süreniz doldu. Talebi yeniden açın.']);
            }
            return $callback();
        });
    }

    private function assertAvailable(Ticket $ticket, ?string $token): void
    {
        if ($ticket->edit_lock_token && $ticket->edit_lock_expires_at > now()->toDateTimeString()
            && ($ticket->edit_lock_token !== $token || (int) $ticket->edit_lock_user_id !== (int) auth()->id())) {
            $name = User::find($ticket->edit_lock_user_id)?->name ?? 'Başka bir kullanıcı';
            throw ValidationException::withMessages(['edit_lock' => "Bu talep şu anda {$name} tarafından açık tutuluyor. Kullanıcı talebi bıraktığında tekrar deneyin."]);
        }
    }
}
