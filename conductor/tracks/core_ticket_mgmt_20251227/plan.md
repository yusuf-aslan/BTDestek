# Plan: Core Ticket Management & Public Portal

## Phase 1: Database & Models
- [x] Task: Create Migrations for Departments, Categories, Tickets, Announcements, and Canned Responses. <!-- bed20a6 -->
- [ ] Task: Implement Models with appropriate relationships and fillable fields.
- [ ] Task: Implement `Ticket::booted()` logic for auto-capturing `ip_address` and `computer_name`.
- [ ] Task: Create a `DatabaseSeeder` with Turkish defaults for Departments and Categories.
- [ ] Task: Conductor - User Manual Verification 'Phase 1: Database & Models' (Protocol in workflow.md)

## Phase 2: Backend (Filament) Implementation
- [ ] Task: Generate Filament Resources for Department, Category, Announcement, and CannedResponse.
- [ ] Task: Generate `TicketResource` with custom List and Form layouts (Turkish labels).
- [ ] Task: Implement Canned Response helper logic in the Ticket form.
- [ ] Task: Create a Dashboard Widget for active Announcements.
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Backend Implementation' (Protocol in workflow.md)

## Phase 3: Public Portal Implementation
- [ ] Task: Create the Landing Page with Announcement alerts and Ticket Submission form.
- [ ] Task: Implement Ticket Tracking (Sorgulama) functionality.
- [ ] Task: Ensure styling matches the 'Professional & Clinical' guidelines using Tailwind.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Public Portal' (Protocol in workflow.md)
