# Specification: Refinement: Replace Computer Name with Phone Number

## Overview
This track modifies the ticket data structure and forms to replace the automatically captured `computer_name` with a manually entered `phone_number` (Dahili No). This is more practical for hospital IT support where reaching the user via internal phone is critical.

## Goals
- Remove `computer_name` auto-capture logic and field from forms.
- Add `phone_number` field to the Ticket entity.
- Update Public Submission Form to require a phone number.
- Update Filament Ticket Resource to display and edit phone number instead of computer name.

## Requirements
- **Database:** Remove `computer_name`, Add `phone_number` (string, required).
- **Model:** Update `Ticket` fillable and `booted` logic.
- **Frontend:** Update `welcome.blade.php` to ask for "Dahili No / Telefon".
- **Backend:** Update `TicketForm` and `TicketsTable`.
