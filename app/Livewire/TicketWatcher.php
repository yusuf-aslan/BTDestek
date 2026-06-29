<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class TicketWatcher extends Component
{
    public $lastCount = 0;

    public function mount()
    {
        $this->lastCount = $this->getTicketCount();
    }

    public function getTicketCount()
    {
        $user = Auth::user();
        if (!$user) return 0;

        $query = Ticket::query()->whereNotIn('status', ['çözüldü', 'iptal']);
        
        if (!$user->is_admin) {
            $query->whereHas('category', function ($q) use ($user) {
                $q->whereHas('users', function ($u) use ($user) {
                    $u->where('users.id', $user->id);
                });
            });
        }
        
        return $query->count();
    }

    public function checkNewTickets()
    {
        $currentCount = $this->getTicketCount();
        $isNew = $currentCount > $this->lastCount;

        if ($isNew) {
            $this->showUrgentNotification();
        }

        // Sayı değiştiyse diğer bileşenlere haber ver
        if ($currentCount != $this->lastCount) {
            $this->dispatch('refresh-active-tickets');
        }

        $this->dispatch('ticket-count-updated', count: $currentCount, isNew: $isNew);
        $this->lastCount = $currentCount;
    }

    public function showUrgentNotification()
    {
        Notification::make()
            ->title('YENİ DESTEK TALEBİ!')
            ->icon('heroicon-o-bell-alert')
            ->iconColor('info')
            ->body('Sisteme yeni bir talep düştü. Lütfen kontrol edin.')
            ->actions([
                Action::make('view')
                    ->label('Talepleri Görüntüle')
                    ->url(route('filament.admin.resources.tickets.index'))
                    ->button()
                    ->color('info'),
            ])
            ->persistent()
            ->send();
    }

    public function render()
    {
        return <<<'BLADE'
            <div x-data="{
                baseTitle: '',
                audio: null,
                init() {
                    this.baseTitle = document.title.replace(/^(\d+)\s/, '');
                    this.audio = new Audio('/sounds/notification.mp3');
                    
                    if ('Notification' in window && Notification.permission === 'default' && window.location.protocol === 'https:') {
                        Notification.requestPermission();
                    }

                    this.$wire.checkNewTickets();
                    setInterval(() => this.$wire.checkNewTickets(), 30000);

                    window.addEventListener('ticket-count-updated', (event) => {
                        this.updateDisplay(event.detail.count, event.detail.isNew);
                    });
                },
                updateDisplay(totalCount, isNew) {
                    const isTicketsPage = window.location.pathname.includes('/admin/tickets') && !window.location.pathname.includes('/edit') && !window.location.pathname.includes('/create');
                    localStorage.setItem('current_total_count', totalCount);

                    if (isTicketsPage) {
                        localStorage.setItem('last_seen_ticket_count', totalCount);
                    }

                    const lastSeen = parseInt(localStorage.getItem('last_seen_ticket_count') || 0);
                    const unread = Math.max(0, totalCount - lastSeen);

                    if (unread > 0 && !isTicketsPage) {
                        document.title = `(${unread}) ${this.baseTitle}`;
                    } else {
                        document.title = this.baseTitle;
                    }

                    if (isNew) {
                        // Sesli bildirim (HTTP/HTTPS farketmez)
                        this.audio.play().catch(e => console.log('Ses çalınamadı, etkileşim gerekiyor.'));

                        // Tarayıcı bildirimi (Sadece HTTPS)
                        if (document.hidden && window.location.protocol === 'https:' && Notification.permission === 'granted') {
                            new Notification('🔔 YENİ TALEP!', {
                                body: 'Sisteme yeni bir destek talebi düştü.',
                                icon: '/favicon.ico'
                            }).onclick = () => { window.focus(); };
                        }

                        // Otomatik Sayfa Yenileme (Eğer Talepler listesindeyse)
                        if (isTicketsPage) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }

                    // Sidebar Badge güncelleme
                    const ticketsLink = document.querySelector('a[href*=\'/admin/tickets\']');
                    if (ticketsLink) {
                        const badge = ticketsLink.querySelector('.fi-sidebar-item-badge, .fi-badge');
                        if (badge) {
                            badge.innerText = totalCount;
                            badge.classList.toggle('hidden', totalCount === 0);
                            if (totalCount > 0) badge.style.display = '';
                        }
                    }
                }
            }" class="hidden">
            </div>
        BLADE;
    }
}