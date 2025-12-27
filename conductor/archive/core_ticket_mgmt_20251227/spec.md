# Specification: Core Ticket Management & Public Portal

## Overview
This track implements the foundational elements of the Hospital IT Support system (Hastane BT Destek). It includes the database schema, the Filament-based administration panel for technicians, and a public-facing portal for hospital staff to submit and track requests without logging in.

## Data Models & Schema

### 1. Department (Bölüm)
- `name`: string (e.g., Yazılım, Donanım, Tıbbi Cihaz)

### 2. Category (Kategori)
- `name`: string (e.g., Yazıcı, HBYS, İnternet)
- `description`: text (optional)

### 3. Ticket (Talep)
- `tracking_number`: string (unique, e.g., BT-2025-XXXX)
- `name`: string (requester name)
- `department_room`: string (requester location/unit)
- `category_id`: foreignId (Category)
- `subject`: string
- `description`: text
- `status`: enum (yeni, işlemde, beklemede, çözüldü, iptal)
- `priority`: enum (düşük, orta, yüksek, acil)
- `ip_address`: string
- `computer_name`: string
- `resolved_at`: timestamp (optional)

### 4. Announcement (Duyuru)
- `title`: string
- `content`: text
- `type`: enum (info, warning, danger)
- `is_active`: boolean

### 5. CannedResponse (Hazır Cevap)
- `title`: string
- `content`: text

## Features

### Public Portal
- **Landing Page:** Displays active `Announcements` as colored alerts.
- **Submit Ticket Form:** 
    - Fields: Name, Department/Room, Category, Subject, Description.
    - Logic: Automatically capture `ip_address` and `computer_name` on the backend.
    - Result: Display the generated `tracking_number` to the user.
- **Track Ticket:** Simple form to search by `tracking_number` and see the current status/history.

### Backend (Filament)
- **TicketResource:**
    - List view with color-coded priority and status badges.
    - Edit view with a "Tech Section" for Status, Priority, and Canned Response selection.
- **Simple CRUDs:** Resources for Departments, Categories, Announcements, and Canned Responses.
- **Dashboard Widget:** Display active announcements to technicians.

## Technical Constraints
- All UI labels in Turkish.
- Use Filament 4.0 and Laravel 12.
- Automatically populate IP/Hostname in the Ticket model's `booted` method.
