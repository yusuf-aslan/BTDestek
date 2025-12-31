# Specification: File Attachments

## Goal
Enable users to attach files (images, PDFs, logs) to their support tickets to provide better context for IT technicians.

## Requirements
1.  **Multiple Files:** Users can upload multiple files (limit: 3 files).
2.  **File Types:** Allow images (jpg, png, webp, jpeg) and documents (pdf, txt, log).
3.  **Size Limit:** Max 5MB per file.
4.  **Security:** Files should be stored in a private directory (`storage/app/attachments`) to prevent unauthorized public access. Only authenticated technicians (via Filament) should be able to download/view them.
5.  **Frontend:** Update the Ticket form on `welcome.blade.php` to accept files.
6.  **Backend:** Update `TicketController` to handle file validation and storage.
7.  **Admin:** Update Filament `TicketResource` to list attachments with download links.

## Data Model
**New Table:** `ticket_attachments`
- `id`
- `ticket_id` (FK, constrained, onDelete cascade)
- `file_path` (string) - Relative path in storage
- `file_name` (string) - Original filename
- `file_type` (string) - MIME type
- `file_size` (integer) - Size in bytes
- `created_at`, `updated_at`

## Technical Implementation
- **Migration:** Create `ticket_attachments` table.
- **Model:** Create `TicketAttachment` model. Update `Ticket` model with `hasMany` relationship.
- **Controller:** 
    - Validation: `files.* => 'required|file|mimes:jpg,jpeg,png,webp,pdf,txt,log|max:5120'`
    - Storage: `Storage::disk('local')->put('attachments', $file)`
- **Filament:** 
    - Use `Infolists` or a Relation Manager to show attachments.
    - Since files are private, we need a route to download them: `Route::get('/tickets/attachments/{attachment}', ...)` protected by auth middleware.
