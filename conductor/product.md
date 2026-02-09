# Initial Concept

## Product Name
Hastane BT Destek (Hospital IT Support)

## Vision
To build a comprehensive, hybrid IT support ecosystem for a hospital environment. The system comprises a public-facing portal for staff to easily report issues without barriers and a robust backend for IT technicians to manage, track, and resolve these requests efficiently.

## Language & Localization
- **Primary Language:** Turkish (Türkçe)
- All UI elements, public pages, notifications, and database data will be in Turkish.

## Architecture
-   **Frontend (Public Portal):** A lightweight, responsive interface (Blade/Livewire) accessible to all hospital staff without requiring login.
-   **Backend (Admin/Technician Panel):** A secure, feature-rich management dashboard built with **Filament**, accessible only to IT staff and administrators.

## Core Features

### 1. Public Portal (Personel Arayüzü)
-   **Guest Ticket Submission:** Staff can submit support requests without registering or logging in.
    -   *Fields:* Name, Department/Room, Category, Subject, Description, Attachments (Screenshots/Photos).
-   **Ticket Tracking (Talep Sorgulama):** Users receive a unique **Ticket Tracking Number** (e.g., `#BT-2024-8592`) upon submission. They can use this number (combined with their email/phone) to check the status of their request.
-   **Public Announcements:** Critical system alerts and maintenance notifications are displayed prominently on the landing page.
-   **Knowledge Base Access:** Read-only access to helpful guides and FAQ to encourage self-resolution.

### 2. Backend & Ticket Management (Yönetim Paneli)
-   **Queue Management:** Technicians view a dashboard of incoming tickets, filterable by priority, category, and status.

-   **Workflow & Routing:**
    -   **Departments:** Routing to Hardware, Software, Network, or HBYS teams.
    -   **Status:** Yeni (New), İnceleniyor (Processing), Beklemede (Pending), Çözüldü (Resolved), İptal (Cancelled).
-   **Canned Responses:** Rapid response templates for common issues, auto-filling a **Resolution Note** for technicians.

### 3. Advanced Features (Gelişmiş Özellikler)
-   **Notifications:** Automated Email (and optional SMS) notifications to the user when their ticket is received, updated, or closed.
-   **SLA Tracking (Hizmet Seviyesi):** Visual indicators for tickets that have been open too long based on their priority (e.g., "Critical" tickets turn red after 1 hour).
-   **Asset Association:** Technicians can link a ticket to a specific inventory item (e.g., "MRI Console PC #4") for maintenance history.
-   **File Attachments:** Secure handling of screenshot or error log uploads during ticket creation.
-   **Feedback Loop:** Optional "Satisfaction Survey" sent to the user after a ticket is closed.

### 4. User Roles
-   **Guest:** Hospital Staff (Public access).
-   **Technician:** Can view/edit tickets, manage assets, view internal docs.
-   **Admin:** Full system configuration, user management, detailed reporting.
