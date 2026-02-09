# Plan: Refinement: Replace Computer Name with Phone Number

## Phase 1: Database & Model Refactoring
- [x] Task: Create migration to add `phone_number` to `tickets` table. <!-- 40547e8 -->
- [x] Task: Update `Ticket` model (fillable fields) and related logic for hostname. <!-- 3c6d472 -->
- [x] Task: Conductor - User Manual Verification 'Phase 1: Database & Model' (Protocol in workflow.md)

## Phase 2: Frontend & Backend Updates
- [x] Task: Update Public Portal (`welcome.blade.php`, `TicketController`) to capture `phone_number`. <!-- e5b22cc -->
- [x] Task: Update Filament `TicketResource` (Form and Table) to use Phone Number. <!-- 0f7718e -->
- [x] Task: Conductor - User Manual Verification 'Phase 2: Frontend & Backend' (Protocol in workflow.md)
