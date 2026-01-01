<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Filament\Notifications\Notification;
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

        if ($currentCount > $this->lastCount) {
            Notification::make()
                ->title('YENİ DESTEK TALEBİ VAR!')
                ->icon('heroicon-o-bell-alert')
                ->iconColor('info')
                ->body('Sisteme yeni bir talep düştü. Detayları incelemek için aşağıdaki butona tıklayın.')
                ->actions([
                    \Filament\Actions\Action::make('view')
                        ->label('Talepleri Görüntüle')
                        ->url(route('filament.admin.resources.tickets.index'))
                        ->button()
                        ->color('info'),
                ])
                ->persistent() // Siz kapatana kadar ekranda kalır
                ->send();

            $this->dispatch('play-notification-sound');
            $this->dispatch('update-nav-badge', count: $currentCount);
        }

        $this->lastCount = $currentCount;
    }

    public function render()
    {
        return <<<'BLADE'
            <div wire:poll.20s="checkNewTickets" class="hidden">
                <audio id="notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3" preload="auto"></audio>
                <script>
                    window.addEventListener('play-notification-sound', () => {
                        const audio = document.getElementById('notification-sound');
                        if (audio) {
                            audio.play().catch(e => console.log('Ses çalınamadı (Tarayıcı engeli olabilir):', e));
                        }
                    });
                    
                    window.addEventListener('update-nav-badge', (event) => {
                        // Sidebar'daki Talepler linkini bul
                        const ticketsLink = document.querySelector('a[href*="/admin/tickets"]');
                        if (ticketsLink) {
                            // Badge elementini bul veya oluştur
                            let badge = ticketsLink.querySelector('.fi-sidebar-item-badge');
                            if (badge) {
                                badge.innerText = event.detail.count;
                            } else {
                                // Eğer daha önce 0 olduğu için badge yoksa, sayfayı yenilemek en güvenlisi
                                window.location.reload();
                            }
                        }
                    });
                </script>
            </div>
        BLADE;
    }
}
