# Specification: Announcement Templates & Scheduling

## Goal
Streamline the announcement creation process by allowing admins to use predefined templates and save new announcements as templates. Also, display the publication time for better context.

## Requirements

### 1. Announcement Templates
-   **New Model:** `AnnouncementTemplate`
    -   `title` (string)
    -   `content` (text)
    -   `type` (enum: info, warning, danger)
-   **Admin Panel:** Create a resource to Manage Templates (Create, Read, Update, Delete).

### 2. Enhanced Announcement Creation (Filament)
-   **Load from Template:** In `AnnouncementResource`, add an action or select input to populate the form fields (`title`, `content`, `type`) from a selected `AnnouncementTemplate`.
-   **Save as Template:** Add a checkbox or action "Save as Template" when creating/editing an announcement. If checked, a new `AnnouncementTemplate` is created from the submitted data.
-   **Delete:** Ensure standard Delete actions are available (Filament defaults usually cover this).

### 3. Publication Timestamp
-   **Database:** Add `published_at` (datetime, nullable) to `announcements` table.
-   **Logic:** Default `published_at` to `now()` when creating a new active announcement.
-   **Admin Panel:** Add `published_at` DateTimePicker to the form.
-   **Frontend:** Display `published_at` formatted (e.g., "29.12.2025 14:30") in the announcement card/modal.

## Technical Implementation

### Database
-   Migration: `create_announcement_templates_table`
-   Migration: `add_published_at_to_announcements_table`

### Models
-   `AnnouncementTemplate`
-   Update `Announcement` (fillable `published_at`)

### Filament
-   `AnnouncementTemplateResource`: Standard CRUD.
-   `AnnouncementResource`:
    -   Use `Filament\Forms\Components\Select` with `live()` and `afterStateUpdated` to fill other fields when a template is selected.
    -   Use `lifecycle` hooks or a dedicated checkbox to handle "Save as Template".

### Frontend
-   Update `welcome.blade.php` to show `$announcement->published_at`.
