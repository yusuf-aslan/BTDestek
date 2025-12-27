# Specification: Localization: Turkish Admin Panel

## Overview
This track focuses on configuring the application to use Turkish (tr) as the primary language. This will localize the Filament Admin Panel interface, standard Laravel validation messages, and date formats.

## Goals
- Set application locale to `tr`.
- Ensure standard Laravel validation messages are in Turkish.
- Verify Filament Admin Panel uses Turkish translation strings.

## Requirements
- `config/app.php` should have `locale` set to `tr`.
- `config/app.php` should have `timezone` set to `Europe/Istanbul`.
- Validation messages (e.g., "The name field is required") should be in Turkish ("name alanı gereklidir").
