# Specification: Refinement: Replace Computer Name with Phone Number

## Overview
This track modifies the ticket data structure and forms to use a manually entered `phone_number` (Dahili No) instead of an automatically captured computer name. This is more practical for hospital IT support where reaching the user via internal phone is critical.

## Goals

- Add `phone_number` field to the Ticket entity.
- Update Public Submission Form to require a phone number.
- Update Filament Ticket Resource to display and edit phone number.

## Requirements
- **Database:** Remove `computer_name` (if exists), Add `phone_number` (string, required).
- **Model:** Update `Ticket` fillable and `booted` logic.
- **Frontend:** Update `welcome.blade.php` to ask for "Dahili No / Telefon".
- **Backend:** Update `TicketForm` and `TicketsTable`.
