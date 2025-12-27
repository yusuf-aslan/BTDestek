# Plan: Core Ticket Management & Public Portal

## Phase 1: Database & Models [checkpoint: 7ee1906]
- [x] Task: Create Migrations for Departments, Categories, Tickets, Announcements, and Canned Responses. <!-- bed20a6 -->
- [x] Task: Implement Models with appropriate relationships and fillable fields. <!-- 90cecd0 -->
- [x] Task: Implement `Ticket::booted()` logic for auto-capturing `ip_address` and `computer_name`. <!-- ea7bace -->
- [x] Task: Create a `DatabaseSeeder` with Turkish defaults for Departments and Categories. <!-- a5d00c5 -->
- [x] Task: Conductor - User Manual Verification 'Phase 1: Database & Models' (Protocol in workflow.md) <!-- 7ee1906 -->

## Phase 2: Backend (Filament) Implementation
- [x] Task: Generate Filament Resources for Department, Category, Announcement, and CannedResponse. <!-- f384f21 -->
- [ ] Task: Generate `TicketResource` with custom List and Form layouts (Turkish labels).
- [ ] Task: Implement Canned Response helper logic in the Ticket form.
- [ ] Task: Create a Dashboard Widget for active Announcements.
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Backend Implementation' (Protocol in workflow.md)

## Phase 3: Public Portal Implementation
- [ ] Task: Create the Landing Page with Announcement alerts and Ticket Submission form.
- [ ] Task: Implement Ticket Tracking (Sorgulama) functionality.
- [ ] Task: Ensure styling matches the 'Professional & Clinical' guidelines using Tailwind.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Public Portal' (Protocol in workflow.md)
