# Plan: Asset Management (Envanter Yönetimi)

## Objective
Implement a robust IT Asset Management (ITAM) module to track hardware inventory (Computers, Printers, etc.), manage their lifecycle, and integrate them with the existing ticketing system. The module must support importing legacy data (PC No, RAM, Monitor, etc.) while offering a flexible structure for future asset types.

## Strategy
1.  **Flexible Schema:** Use a JSON column for technical specifications (`specs`) to store variable data (RAM for PCs, Toner for Printers) without creating dozens of sparsely populated columns.
2.  **Filament Resource:** leverage Filament's powerful table and form builders to manage assets.
3.  **Excel Import:** Use Filament's native Import actions or `maatwebsite/excel` to migrate the user's existing inventory data seamlessly.
4.  **Integration:** Add a relation to the `Ticket` model so issues can be linked to specific assets.

## Tasks

### Phase 1: Database & Model Structure
- [x] Create `Asset` model and migration with fields: `name`, `type`, `status`, `department_id`, `location`, `brand`, `model`, `purchase_date`, `warranty_expires_at`, `specs` (JSON), `notes`.
- [x] Define relationships: `Asset` belongs to `Department` and `User`.
- [x] Create `AssetType` enum or hardcoded array (Computer, Printer, Network, Peripheral, MedicalDevice).
- [x] Create `AssetStatus` enum (Active, Maintenance, Retired, InStock).

### Phase 2: Filament Admin Panel (CRUD)
- [x] Generate Filament Resource: `php artisan make:filament-resource Asset`.
- [x] Configure `form()`:
    -   Use `Section` for logical grouping (Identity, Location, Specs).
    -   Use `KeyValue` or specific fields for `specs`.
    -   Use `Select` for relationships (Department, User).
- [x] Configure `table()`:
    -   Columns: Name, Tag, Model, Department, User, Status (Badge).
    -   Filters: Type, Status, Department.
    -   Searchable fields.

### Phase 3: Data Import (Legacy Data)
- [x] Create an Import Class for Filament: `php artisan make:filament-import-header AssetImporter`.
- [x] Map legacy columns (PC No -> name, Ana Birim -> department, RAM/Monitor -> specs) to the new model in the importer logic.
- [x] Add `ImportAction` to the `AssetResource` table header.

### Phase 4: Ticket Integration
- [x] Create migration: `add_asset_id_to_tickets_table`.
- [x] Update `Ticket` model: `belongsTo(Asset)`.
- [x] Update `Asset` model: `hasMany(Ticket)`.
- [x] Update `TicketResource` form: Add `Select::make('asset_id')` (searchable).
- [x] Update `TicketResource` table: Show related asset.
- [ ] Update public ticket form (optional/later): Allow users to select their assigned assets? (Maybe too complex for now, keep for admin/technician first).

### Phase 5: Asset History & Visualization
- [x] Add `RelationManager` to `AssetResource` to show "Ticket History" for a specific asset.
- [x] (Optional) Dashboard widget: "Assets with most tickets".

## Technical Considerations
-   **JSON Specs:** Ensure the `specs` column is cast to `array` in the model.
-   **Department Linking:** The import process needs to fuzzy-match "Ana Birim" names to existing `departments` IDs or create them if missing.
