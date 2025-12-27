# Plan: Refinement: Replace Computer Name with Phone Number

## Phase 1: Database & Model Refactoring
- [x] Task: Create migration to remove `computer_name` and add `phone_number` to `tickets` table. <!-- 40547e8 -->
- [x] Task: Update `Ticket` model (fillable fields) and remove `booted` auto-capture logic for hostname. <!-- 3c6d472 -->
- [~] Task: Conductor - User Manual Verification 'Phase 1: Database & Model' (Protocol in workflow.md)

## Phase 2: Frontend & Backend Updates
- [x] Task: Update Public Portal (`welcome.blade.php`, `TicketController`) to capture `phone_number`. <!-- e5b22cc -->
- [~] Task: Update Filament `TicketResource` (Form and Table) to replace Computer Name with Phone Number.
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Frontend & Backend' (Protocol in workflow.md)
