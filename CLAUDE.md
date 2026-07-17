# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Luminara Transaksi is a Laravel 12 + Alpine.js internal management system for Luminara Photobooth. It handles:
- Booking management with calendar availability
- Invoice generation and financial tracking
- Midtrans payment gateway integration (QRIS, VA, E-Wallet)
- A section-based invitation/template editor ("Studio")
- Public invitation viewing and RSVP

## Common Commands

```bash
# DDEV (recommended local environment)
ddev start
ddev composer setup          # install deps, migrate, build
ddev composer dev            # serve + queue + logs + vite concurrently
ddev composer test
ddev exec php artisan <cmd>

# Native
composer install && npm install && npm run build
composer dev                 # same stack, host machine
composer test
php artisan migrate
php artisan db:seed
npm run build
```

## Architecture

### Dual Frontend System

The app has two Vite entry points sharing the same Laravel backend, both server-rendered via Blade:

1. **`app` entry** (`resources/css/app.css` + `resources/js/app.js`) — booking pages, admin dashboard, invoices. TailwindCSS v4.
2. **`invitation` entry** (`resources/css/invitation.css` + `resources/js/invitation.js`) — the public invitation page (`/invitation/{slug}`). Bundles Alpine.js only, no dev CDNs; Alpine drives the gate, sticky cover, lightbox, countdown, and reveal animations declared inline in the Blade views.

The admin **Studio** editor (`/admin/templates/{id}/studio`, `TemplateEditorController@studio`) is a third surface: Blade + Alpine, but Alpine is loaded from a CDN in `resources/views/layouts/studio.blade.php` rather than bundled via Vite. Its logic lives in a single `x-data="studioApp()"` component in `resources/views/admin/templates/studio.blade.php`, plus `resources/views/admin/templates/studio/_inspector.blade.php` for the props panel.

There is no React frontend and no Monaco-based code editor — both were removed along with `resources/js/editor/`, `editor-native.blade.php`, and the `editor-react` route.

### Invitation/Template Rendering Pipeline

Invitation rendering for public guests is server-side Blade, driven by a structured section tree — **not** a single HTML blob. When a guest visits `/invitation/{slug}`:

1. `InvitationViewController::show()` resolves the invitation page and its template
2. `InvitationRenderer` service (`app/Services/InvitationRenderer.php`) loads the page's `InvitationSection` rows (ordered, `is_visible`) and renders `templates.section-tree`
3. Each section renders via a `resources/views/templates/components/*` Blade partial, keyed by `section_type` and configured in `config/invitation_components.php`

The Studio editor writes to `InvitationTemplate` / `InvitationSection` (JSON `props` column) via the `admin/api` endpoints below; the renderer reads the same rows. The legacy path — one big HTML blob per template in `html_content`/`cover_content` columns — has been removed entirely (columns dropped, `editor-native.blade.php` deleted).

### Public Invitation Shell

The rendered invitation is wrapped by `resources/views/components/invitation/layout.blade.php`:
- `invite-shell` — outer split-pane layout (desktop: `invite-hero` pane on the left with cover photo/couple names, `invite-card` as the sole scroll container on the right)
- `invite-gate` — full-viewport cover gate the guest taps through
- `invite-cover-sticky` — sticky reveal screen shown after the gate opens
- `invite-preloader` — image/font preload splash (skipped in Studio, see below)

In Studio preview (`studioPreview` → `skipCover=true`), `isOpen` starts `true` so the gate never shows in the canvas; the sticky cover screen stays visible and editable there.

### Admin API (within web routes)

Admin API endpoints live in `routes/web.php` under the `admin/api` prefix group (not `routes/api.php`). They handle section CRUD, reordering, and asset uploads for Studio.

The public payment API (`/api/transaction`) does live in `routes/api.php` and is consumed by the Flutter client.

### Multi-Division Data Model

The system supports two business units: `photobooth` and `visual`. Key tables (`bookings`, `packages`, `galleries`) use `business_unit` for segregation. `users.division` drives access control — `super_admin` bypasses division filtering.

### Global Custom CSS

Templates and invitations support a `global_custom_css` column. The InvitationRenderer injects this into the rendered Blade view so admins can apply custom styling without touching component defaults.

### Dependencies

- `midtrans/midtrans-php` — payment gateway
- `intervention/image` — image processing
- `laravel/sanctum` — API token auth
- `alpinejs` — frontend interactivity (public invitation page + Studio editor)
- `sortablejs` — drag-and-drop reordering (Studio section list)
