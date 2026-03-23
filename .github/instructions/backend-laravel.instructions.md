---
description: "Use when editing Laravel backend files, controllers, models, routes, migrations, seeders, or business logic for booking, invoice, and admin APIs."
name: "Backend Laravel Guardrails"
applyTo: "app/**/*.php, routes/**/*.php, database/**/*.php"
---

# Backend Laravel Guardrails

## Scope

- Keep changes small and focused; prefer patch-style fixes over broad refactors.
- Preserve existing route names, payload shapes, and status constants unless task explicitly requires changes.

## Domain Rules

- Enforce multi-division boundaries in data access:
    - users.division controls admin access (super_admin checks are common).
    - business_unit filtering is required where applicable (bookings, packages, galleries).
- Do not remove or break Midtrans endpoints in routes/api.php or related payment integration unless explicitly requested.

## Validation and Safety

- Keep backend as source of truth for booking availability and blocked-date rules.
- Maintain backward compatibility for existing public and admin endpoints.
- If adding new DB fields, include migration + model fillable/casts + validation updates in the same task.

## Implementation Notes

- Prefer Laravel validation and policies/gates style already present in controllers.
- Keep responses and redirects consistent with the existing controller pattern in this repo.
- Avoid hidden behavior changes in invoice totals, booking status transitions, and payment sync flow.

## Verification Checklist

- Run targeted checks with DDEV when possible:
    - ddev composer test
    - ddev exec php artisan route:list (when route changes are made)
    - ddev exec php artisan migrate --pretend (for migration review)
