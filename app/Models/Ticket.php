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

    public function waitingStartedAt(?GeneralSetting $settings = null): \Carbon\Carbon
    {
        $startedAt = $this->created_at->copy();
        $settings ??= \Illuminate\Support\Facades\Cache::rememberForever(
            'general_settings',
            fn () => GeneralSetting::first()
        );

        if ($settings?->isLunchBreakAt($startedAt)) {
            return $settings->lunchBreakEndsAt($startedAt);
        }

        return $startedAt;
    }

    public function elapsedMinutes($endTime = null, ?GeneralSetting $settings = null): int
    {
        $startedAt = $this->waitingStartedAt($settings);
        $endedAt = $endTime ? \Carbon\Carbon::parse($endTime) : now();

        if ($endedAt->lessThanOrEqualTo($startedAt)) {
            return 0;
        }

        return (int) $startedAt->diffInMinutes($endedAt);
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

                    // Kapalı bir talebin teknisyeni sonradan düzeltilirse,
                    // performans istatistiklerini de yeni teknisyene aktar.
                    if (
                        TicketStatus::isClosed($ticket->getOriginal('status'))
                        && (string) $ticket->getOriginal('resolved_by') !== (string) $newAssignedTo
                    ) {
                        $oldResolverId = $ticket->getOriginal('resolved_by');
                        $oldResolver = $oldResolverId ? User::find($oldResolverId) : null;
                        $ticket->resolved_by = $newAssignedTo;

                        TicketActivity::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => Auth::id(),
                            'activity_type' => 'resolver_changed',
                            'properties' => ['old' => $oldResolverId, 'new' => $newAssignedTo],
                            'description' => sprintf(
                                'Çözen teknisyen %s kullanıcısından %s kullanıcısına düzeltildi.',
                                $oldResolver?->name ?? 'belirtilmemiş',
                                $technician?->name ?? 'belirtilmemiş'
                            ),
                        ]);
                    }
                    
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

                if (TicketStatus::isClosed($newStatus)) {
                    if (!$ticket->resolved_at) {
                        $ticket->resolved_at = now();
                    }
                    
                    if (!$ticket->assigned_to) {
                        $ticket->assigned_to = Auth::id();
                        $ticket->assigned_at = now();
                    }

                    $ticket->resolved_by = $ticket->assigned_to;
                } else {
                    $ticket->resolved_at = null;
                    $ticket->resolved_by = null;
                }
            }

        });
    }
}
