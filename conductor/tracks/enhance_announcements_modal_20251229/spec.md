# Specification: Announcement Modal Pop-up

## Goal
Improve the visibility of active announcements by displaying them in a modal dialog immediately when the user visits the portal.

## Requirements
1.  **Library:** Use Alpine.js for lightweight interaction (modal state management).
2.  **Trigger:** The modal should open automatically on page load if there are active announcements (`$announcements->count() > 0`).
3.  **UI Design:**
    *   Backdrop: Semi-transparent blurred background.
    *   Content: Title, Type-based Icon/Color, and Content of the announcement.
    *   Controls: A "Close" button to dismiss the modal.
    *   If multiple announcements exist, they should be stackable or scrollable within the modal, or the modal should show the most recent one (or all of them in a list). *Decision: Show all active announcements in the modal.*
4.  **Persistence (Optional for now):** Ideally, we shouldn't annoy the user on every reload, but for this iteration, showing it on every load is acceptable as per the "urgent" nature of announcements.

## Technical Implementation
-   **File:** `resources/views/welcome.blade.php`
-   **Logic:**
    -   Add Alpine.js CDN script.
    -   Wrap the modal HTML in an `x-data="{ show: true }"` block.
    -   Use `x-show` with transitions for smooth effect.
