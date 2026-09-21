<x-filament-panels::page>
    <style>
        .ticket-lock-status {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            overflow: hidden;
            padding: 15px 18px;
            border: 1px solid;
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        }

        .ticket-lock-status::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            content: '';
        }

        .ticket-lock-status__icon {
            display: grid;
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            place-items: center;
            border-radius: 12px;
        }

        .ticket-lock-status__icon svg {
            width: 22px;
            height: 22px;
        }

        .ticket-lock-status__eyebrow {
            margin: 0 0 2px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.14em;
        }

        .ticket-lock-status__title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
        }

        .ticket-lock-status__description {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .ticket-lock-status--locked {
            border-color: rgba(245, 158, 11, 0.35);
            background: linear-gradient(110deg, rgba(245, 158, 11, 0.11), rgba(245, 158, 11, 0.035));
        }

        .ticket-lock-status--locked::before {
            background: #f59e0b;
        }

        .ticket-lock-status--locked .ticket-lock-status__icon {
            background: rgba(245, 158, 11, 0.16);
            color: #d97706;
        }

        .ticket-lock-status--locked .ticket-lock-status__eyebrow {
            color: #b45309;
        }

        .ticket-lock-status--ready {
            border-color: rgba(16, 185, 129, 0.35);
            background: linear-gradient(110deg, rgba(16, 185, 129, 0.11), rgba(16, 185, 129, 0.035));
        }

        .ticket-lock-status--ready::before {
            background: #10b981;
        }

        .ticket-lock-status--ready .ticket-lock-status__icon {
            background: rgba(16, 185, 129, 0.16);
            color: #059669;
        }

        .ticket-lock-status--ready .ticket-lock-status__eyebrow {
            color: #047857;
        }

        .dark .ticket-lock-status {
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }

        .dark .ticket-lock-status__description {
            color: #94a3b8;
        }

        .dark .ticket-lock-status--locked {
            border-color: rgba(245, 158, 11, 0.28);
            background: linear-gradient(110deg, rgba(245, 158, 11, 0.13), rgba(30, 41, 59, 0.2));
        }

        .dark .ticket-lock-status--locked .ticket-lock-status__icon,
        .dark .ticket-lock-status--locked .ticket-lock-status__eyebrow {
            color: #fbbf24;
        }

        .dark .ticket-lock-status--ready {
            border-color: rgba(16, 185, 129, 0.28);
            background: linear-gradient(110deg, rgba(16, 185, 129, 0.13), rgba(30, 41, 59, 0.2));
        }

        .dark .ticket-lock-status--ready .ticket-lock-status__icon,
        .dark .ticket-lock-status--ready .ticket-lock-status__eyebrow {
            color: #34d399;
        }

        @media (max-width: 640px) {
            .ticket-lock-status {
                align-items: flex-start;
                padding: 14px 15px;
            }
        }
    </style>

    <div wire:poll.15s="refreshLockStatus" class="space-y-6">
        @if($isEditLocked)
            <div class="ticket-lock-status ticket-lock-status--locked">
                <div class="ticket-lock-status__icon">
                    <x-filament::icon icon="heroicon-o-lock-closed" />
                </div>
                <div>
                    <p class="ticket-lock-status__eyebrow">SALT OKUNUR</p>
                    <p class="ticket-lock-status__title">Talep şu anda kullanımda</p>
                    <p class="ticket-lock-status__description">
                        <strong>{{ $lockOwnerName ?: 'Başka bir kullanıcı' }}</strong> bu talebi düzenliyor.
                        İçeriği inceleyebilirsiniz; kilit bırakılana kadar değişiklik yapamazsınız.
                    </p>
                </div>
            </div>
        @else
            <div class="ticket-lock-status ticket-lock-status--ready">
                <div class="ticket-lock-status__icon">
                    <x-filament::icon icon="heroicon-o-lock-open" />
                </div>
                <div>
                    <p class="ticket-lock-status__eyebrow">KİLİT SERBEST</p>
                    <p class="ticket-lock-status__title">Talep düzenlemeye hazır</p>
                    <p class="ticket-lock-status__description">
                        Talebi devralmak için üstteki “Düzenlemeye Geç” düğmesini kullanabilirsiniz.
                    </p>
                </div>
            </div>
        @endif

        {{ $this->content }}
    </div>
</x-filament-panels::page>
