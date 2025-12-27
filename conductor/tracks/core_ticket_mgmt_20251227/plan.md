# Plan: Core Ticket Management & Public Portal

## Phase 1: Database & Models [checkpoint: 7ee1906]
- [x] Task: Create Migrations for Departments, Categories, Tickets, Announcements, and Canned Responses. <!-- bed20a6 -->
- [x] Task: Implement Models with appropriate relationships and fillable fields. <!-- 90cecd0 -->
- [x] Task: Implement `Ticket::booted()` logic for auto-capturing `ip_address` and `computer_name`. <!-- ea7bace -->
- [x] Task: Create a `DatabaseSeeder` with Turkish defaults for Departments and Categories. <!-- a5d00c5 -->
- [x] Task: Conductor - User Manual Verification 'Phase 1: Database & Models' (Protocol in workflow.md) <!-- 7ee1906 -->

## Phase 2: Backend (Filament) Implementation [checkpoint: d71f80e]
- [x] Task: Generate Filament Resources for Department, Category, Announcement, and CannedResponse. <!-- f384f21 -->
- [x] Task: Generate `TicketResource` with custom List and Form layouts (Turkish labels). <!-- eeeeffe -->
- [x] Task: Implement Canned Response helper logic in the Ticket form. <!-- a796dcc -->
- [x] Task: Create a Dashboard Widget for active Announcements. <!-- a1dbf70 -->
- [x] Task: Conductor - User Manual Verification 'Phase 2: Backend Implementation' (Protocol in workflow.md) <!-- d71f80e -->

## Phase 3: Public Portal Implementation
- [x] Task: Create the Landing Page with Announcement alerts and Ticket Submission form. <!-- abf7b39 -->
- [x] Task: Implement Ticket Tracking (Sorgulama) functionality. <!-- 63ee580 -->
- [ ] Task: Ensure styling matches the 'Professional & Clinical' guidelines using Tailwind.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Public Portal' (Protocol in workflow.md)
