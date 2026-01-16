# Plan: Feature Notifications

## Objective
Implement an automated email notification system to keep users informed about the status of their support requests. This increases transparency and reduces the need for users to manually check the portal ("Talep Sorgula").

## Strategy
1.  **Laravel Mailables:** Use standard Laravel Mail classes for clean, testable email logic.
2.  **Markdown Templates:** Use Markdown for professional, responsive email designs.
3.  **Model Observer:** Use a `TicketObserver` to centralize the logic for triggering notifications on model events (`created`, `updated`).
4.  **Asynchronous Sending:** Ensure emails are queued (if queue is configured) or sent so they don't block the HTTP request (for now, standard sync is fine for low volume, but we will write code that supports queues).

## Tasks

### Phase 1: Setup & Configuration
- [x] Verify Mail Configuration (default to `log` driver for local dev).
- [x] Publish Laravel Mail vendor resources (if customization needed).

### Phase 2: Mailables & Views
- [x] Create `TicketCreated` Mailable (Subject: "Destek Talebiniz Alındı: #BT-xxxx").
- [x] Create `TicketResolved` Mailable (Subject: "Talebiniz Çözüldü: #BT-xxxx").
- [x] Create `TicketStatusUpdated` Mailable (Subject: "Talep Durumu Güncellendi: #BT-xxxx").
- [x] Design Markdown templates for each mailable (Greeting, Ticket Info, Button to Track).

### Phase 3: Integration (Observer)
- [x] Create `TicketObserver`.
- [x] Register `TicketObserver` in `AppServiceProvider` or `EventServiceProvider`.
- [x] Implement `created` event: Send `TicketCreated` to `$ticket->email`.

### Phase 4: Database Update (Missing Email Field?)
- [x] **CRITICAL:** Check if `tickets` table has `email` column. If not, add it.
- [x] Migration: `add_email_to_tickets_table`.
- [x] Update `Ticket` model (`fillable`).
- [x] Update Public Form (`welcome.blade.php`) to request Email.
- [x] Update Filament Resource (`TicketResource`) to show/edit Email.

## Notes
-   We need to collect the user's email address to send notifications. The current schema might rely on "Guest" access without email.
-   If `email` is optional, notifications are optional.
