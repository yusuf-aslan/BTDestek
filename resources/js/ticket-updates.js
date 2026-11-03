/**
 * Kullanıcının taleplerindeki değişiklikleri açık sayfaya bildirir.
 * Son görülen durum tarayıcıda saklanır; sayfa kapalıyken olan değişiklik de
 * bir sonraki açılışta bildirilir ve bildirim kapatılana kadar sayfalar arasında kalır.
 */
const STORAGE_KEY = 'ticket-updates';
const POLL_INTERVAL = 30000;
const MAX_WATCHED = 30;

const readState = () => {
    try {
        const state = JSON.parse(localStorage.getItem(STORAGE_KEY));

        return { seen: state?.seen ?? {}, pending: state?.pending ?? {} };
    } catch {
        return { seen: {}, pending: {} };
    }
};

const writeState = (state) => {
    const numbers = Object.keys(state.seen);
    numbers.slice(0, Math.max(0, numbers.length - MAX_WATCHED)).forEach((number) => delete state.seen[number]);

    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch {
        // Tarayıcı depolaması kapalıysa bildirimler yalnızca bu sayfa açıkken çalışır
    }
};

// Kullanıcı talebin kendisine bakıyorsa ayrıca bildirim göstermeye gerek yok
const isOnScreen = (number) => {
    const element = document.querySelector(`[data-open-ticket="${CSS.escape(number)}"]`);

    return Boolean(element && element.getClientRects().length > 0);
};

export default ({ url, opened = [], viewed = null }) => ({
    pending: [],
    memory: null,
    baseTitle: document.title,

    init() {
        const state = readState();
        opened.forEach((number) => {
            state.seen[number] ??= null;
        });
        if (viewed) {
            delete state.pending[viewed];
        }
        this.save(state);

        window.addEventListener('storage', (event) => {
            if (event.key === STORAGE_KEY) {
                this.show(readState());
            }
        });

        this.poll();
        setInterval(() => this.poll(), POLL_INTERVAL);
    },

    async poll() {
        const params = new URLSearchParams();
        Object.keys(readState().seen).forEach((number) => params.append('numbers[]', number));

        let tickets;
        try {
            const response = await fetch(`${url}?${params}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                return;
            }
            ({ tickets } = await response.json());
        } catch {
            return; // Bağlantı kopmuşsa bir sonraki denemede yakalanır
        }

        // İstek sürerken başka sekme yazmış olabilir, güncel hali üzerinden ilerle
        const state = readState();

        tickets.forEach((ticket) => {
            const number = ticket.tracking_number;
            const lastSeen = state.seen[number] ?? null;

            state.seen[number] = ticket.signature;

            if (isOnScreen(number)) {
                delete state.pending[number];
            } else if ((lastSeen !== null && lastSeen !== ticket.signature) || state.pending[number]) {
                state.pending[number] = {
                    number,
                    heading: ticket.heading,
                    subject: ticket.subject,
                    status: ticket.status_label,
                    badge: ticket.badge_classes,
                    note: ticket.note,
                    url: ticket.url,
                };
            }

            this.refreshBadges(ticket);
            window.dispatchEvent(new CustomEvent('ticket-updated', { detail: ticket }));
        });

        this.save(state);
    },

    refreshBadges(ticket) {
        document.querySelectorAll(`[data-ticket-badge="${CSS.escape(ticket.tracking_number)}"]`).forEach((badge) => {
            badge.className = `${badge.dataset.base} ${ticket.badge_classes}`;
            badge.textContent = ticket.status_label;
        });
    },

    dismiss(number) {
        const state = readState();
        delete state.pending[number];
        this.save(state);
    },

    save(state) {
        writeState(state);
        this.show(state);
    },

    show(state) {
        this.pending = Object.values(state.pending);

        const count = this.pending.length;
        document.title = count
            ? `(${count}) ${count === 1 ? this.pending[0].heading : 'Talepleriniz güncellendi'} - ${this.baseTitle}`
            : this.baseTitle;
    },
});
