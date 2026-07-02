# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Luminara Transaksi is a Laravel 12 + React internal management system for Luminara Photobooth. It handles:
- Booking management with calendar availability
- Invoice generation and financial tracking
- Midtrans payment gateway integration (QRIS, VA, E-Wallet)
- Drag-and-drop invitation/template editor (React)
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

The app has two separate frontend stacks sharing the same Laravel backend:

1. **Blade + TailwindCSS v4** — booking pages, admin dashboard, invoices. Entry via `resources/js/app.js`. Rendered server-side via Laravel routes.
2. **Alpine.js + Monaco Editor** — invitation/template visual editor. Entry via `resources/js/editor/app.js`, loaded at `/admin/templates/{id}/editor`. A Blade page (`editor-native.blade.php`) with an Alpine.js component (`editorApp()`) combining a Monaco code editor and a live visual canvas kept in sync with each other.

Vite is configured with two separate entry points in `vite.config.js`. (A React-based editor was planned — the route `/admin/templates/{id}/editor-react` and some now-removed dependencies referenced it — but it was never built; the route redirects to the real Alpine/Monaco editor.)

### Invitation/Template Rendering Pipeline

Invitation rendering for public guests is server-side Blade, **not React**. When a guest visits `/invitation/{slug}`:

1. `InvitationViewController::show()` resolves the invitation and template
2. `InvitationRenderer` service (`app/Services/InvitationRenderer.php`) builds section data
3. Blade views in `resources/views/templates/` render each section type as a component

The React editor writes to `InvitationTemplate` / `InvitationSection` DB tables. The renderer reads them and produces HTML.

### Editor State and Persistence

The live editor in `resources/js/editor/` is a set of plain Alpine.js modules merged into one `editorApp()` component (`app.js` composes `core.js`, `hover.js`, `inspector.js`, `box-model.js`, `init.js`). It edits one big HTML blob per template (`html_content`, `cover_content`, `global_custom_css` columns on `invitation_templates`), not a structured section tree — the visual canvas and the Monaco code editor are kept in sync via `window.syncToMonaco()` / `window.syncToCanvas()`.

A structured, section-tree-based data model (`InvitationSection` rows with JSON `props`, rendered via `templates.components.*` Blade partials driven by `config/invitation_components.php`) exists alongside this and is the direction template rendering is migrating toward — see `docs/superpowers/specs/2026-07-02-template-data-model-design.md`. `InvitationEditorController` and `TemplateEditorController`'s section CRUD endpoints implement it, but no frontend currently drives it.

### Admin API (within web routes)

Admin API endpoints live in `routes/web.php` under the `admin/api` prefix group (not `routes/api.php`). They handle section CRUD, reordering, and asset uploads for the editor.

The public payment API (`/api/transaction`) does live in `routes/api.php` and is consumed by the Flutter client.

### Multi-Division Data Model

The system supports two business units: `photobooth` and `visual`. Key tables (`bookings`, `packages`, `galleries`) use `business_unit` for segregation. `users.division` drives access control — `super_admin` bypasses division filtering.

### Global Custom CSS

Templates and invitations support a `global_custom_css` column. The InvitationRenderer injects this into the rendered Blade view so admins can apply custom styling without touching component defaults.

### Dependencies

- `midtrans/midtrans-php` — payment gateway
- `intervention/image` — image processing
- `laravel/sanctum` — API token auth
- `@tanstack/react-query` — data fetching in the React editor
- `zustand` + `immer` — editor state management
- `react-dnd` + `react-dnd-html5-backend` — drag-and-drop
