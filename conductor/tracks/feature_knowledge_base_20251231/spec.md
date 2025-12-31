# Specification: Knowledge Base (Bilgi Bankası)

## Goal
To reduce the volume of incoming support tickets by providing a searchable, read-only library of "How-to" guides, FAQs, and troubleshooting steps accessible to all hospital staff without login.

## Requirements

### 1. Data Model (`Article`)
-   **Title:** String, required.
-   **Slug:** String, unique, auto-generated from title.
-   **Content:** Long text (Rich Text / Markdown), required.
-   **Category:** Belongs to an existing `Category` (e.g., Hardware, Software).
-   **Is Published:** Boolean (Draft/Published).
-   **Published At:** Timestamp (for scheduling or sorting).
-   **Views:** Integer (counter).

### 2. Admin Panel (Filament)
-   **Resource:** `ArticleResource`
-   **Features:**
    -   Create/Edit/Delete articles.
    -   Rich Text Editor for content (images, lists, bold/italic).
    -   Filter by Category and Published status.
    -   "Publish/Unpublish" toggles.

### 3. Public Portal
-   **Landing Page Section:** "Hızlı Çözümler" (Quick Solutions) section on `welcome.blade.php` showing popular or pinned articles.
-   **Dedicated KB Page:** `/bilgi-bankasi` (Knowledge Base)
    -   Search bar (live search preferred).
    -   List of categories.
    -   List of articles within categories.
-   **Article Detail Page:** `/bilgi-bankasi/{slug}`
    -   Clean, readable layout.
    -   "Was this helpful?" (Simple Yes/No counter - optional MVP).
    -   Related articles (optional).

## Technical Implementation
-   **Route:** `Route::get('/bilgi-bankasi', ...)` and `Route::get('/bilgi-bankasi/{slug}', ...)`.
-   **Controller:** `ArticleController`.
-   **Search:** Simple `LIKE %query%` on title and content for MVP.
