# Plan: Feature: Announcement Templates & Scheduling

## Phase 1: Database & Models
- [x] Task: Create migration for `announcement_templates` and update `announcements`. <!-- e5370f1 -->
- [x] Task: Create `AnnouncementTemplate` model and update `Announcement` model. <!-- a0d14b2 -->
- [~] Task: Conductor - User Manual Verification 'Phase 1: Database & Models' (Protocol in workflow.md)

## Phase 2: Admin Panel (Filament)
- [ ] Task: Create `AnnouncementTemplateResource` for managing templates.
- [ ] Task: Update `AnnouncementResource` to support loading templates, saving as template, and `published_at`.
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Admin Panel' (Protocol in workflow.md)

## Phase 3: Frontend Updates
- [ ] Task: Update `welcome.blade.php` to display announcement publication time.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Frontend Updates' (Protocol in workflow.md)
