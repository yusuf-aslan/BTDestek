# Specification: Show IP in Footer

## Goal
To assist IT support in identifying the user's machine during remote support sessions, the user's IP address should be clearly displayed in the footer of the public portal.

## Requirements
1.  **Location:** Public Portal (`welcome.blade.php`) - Footer section.
2.  **Content:** "IP Adresiniz: [User IP]"
3.  **Style:** Centered, distinct/bold text, possibly with a subtle icon.
4.  **Backend Verification:** Ensure that `Ticket` creation effectively captures this IP (this logic already exists but should be verified).

## Technical Implementation
-   **File:** `resources/views/welcome.blade.php`
-   **Helper:** Use `request()->ip()` to retrieve the IP.
