<?php

namespace App\Models;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    protected $fillable = [
        'tracking_number',
        'name',
        'email',
        'department_room',
        'phone_number',
        'category_id',
        'asset_id',
        'subject',
        'description',
        'status',
        'priority',
        'resolution_note',
        'ip_address',
        'broken_pc_ip',
        'resolved_at',
        'resolved_by',
        'assigned_to',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'assigned_at' => 'datetime',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TicketNote::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->latest();
    }

    protected static function booted(): void
    {
        static::created(function (Ticket $ticket) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'activity_type' => 'created',
                'description' => 'Talep oluşturuldu.',
            ]);
        });

        static::creating(function (Ticket $ticket) {
            $ticket->ip_address = request()->ip();
        });

        static::updating(function (Ticket $ticket) {
            $user = Auth::user();

            // Log assignment change and send notification
            if ($ticket->isDirty('assigned_to')) {
                $oldAssignedTo = $ticket->getOriginal('assigned_to');
                $newAssignedTo = $ticket->assigned_to;

                if ($newAssignedTo) {
                    $ticket->assigned_at = now();
                    if ($ticket->status === 'yeni') {
                        $ticket->status = 'işlemde';
                    }

                    $technician = User::find($newAssignedTo);
                    
                    TicketActivity::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => Auth::id(),
                        'activity_type' => 'assigned',
                        'properties' => ['old' => $oldAssignedTo, 'new' => $newAssignedTo],
                        'description' => $technician ? "Talep {$technician->name} kullanıcısına atandı." : "Talep bir teknisyene atandı.",
                    ]);

                    // Send In-App Notification to the assigned technician
                    if ($technician && $newAssignedTo !== Auth::id()) {
                        Notification::make()
                            ->title('Yeni Talep Atandı')
                            ->body("{$ticket->tracking_number} nolu talep üzerinize atandı: {$ticket->subject}")
                            ->icon('heroicon-o-ticket')
                            ->iconColor('success')
                            ->actions([
                                Action::make('view')
                                    ->label('Görüntüle')
                                    ->url(fn () => route('filament.admin.resources.tickets.edit', $ticket)),
                            ])
                            ->sendToDatabase($technician);
                    }
                } else {
                    $ticket->assigned_at = null;
                    TicketActivity::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => Auth::id(),
                        'activity_type' => 'unassigned',
                        'description' => 'Talebin ataması kaldırıldı.',
                    ]);
                }
            }

            // Log status change
            if ($ticket->isDirty('status')) {
                $oldStatus = $ticket->getOriginal('status');
                $newStatus = $ticket->status;

                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'status_changed',
                    'properties' => ['old' => $oldStatus, 'new' => $newStatus],
                    'description' => "Talep durumu '{$oldStatus}' durumundan '{$newStatus}' durumuna güncellendi.",
                ]);

                if ($newStatus === 'çözüldü') {
                    if (!$ticket->resolved_at) {
                        $ticket->resolved_at = now();
                    }
                    
                    if (!$ticket->assigned_to) {
                        $ticket->assigned_to = Auth::id();
                        $ticket->assigned_at = now();
                    }

                    $ticket->resolved_by = $ticket->assigned_to;
                }
            }

            // If resolved_at is provided (from DatePicker), automatically append current time
            if ($ticket->isDirty('resolved_at') && $ticket->resolved_at) {
                $ticket->resolved_at = \Carbon\Carbon::parse($ticket->resolved_at)->setTime(now()->hour, now()->minute, now()->second);
            }
        });
    }
}