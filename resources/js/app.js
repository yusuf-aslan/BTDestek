import './bootstrap';
import Alpine from 'alpinejs';
import ticketUpdates from './ticket-updates';

window.Alpine = Alpine;
Alpine.data('ticketUpdates', ticketUpdates);
Alpine.start();
