# Project Guidelines

## Mission Context

- This project is an internal system for Luminara Photobooth: booking management, invoice/admin operations, and invitation template editor development.
- Current priority includes stabilizing booking flows and invitation editor features without disruptive rewrites.
- Prefer incremental, low-risk changes over broad refactors unless explicitly requested.

## Build and Test

- Preferred local environment is DDEV.
- Start environment: ddev start
- Initial setup: ddev composer setup
- Run app stack (Laravel server, queue listener, logs, Vite): ddev composer dev
- Run tests: ddev composer test
- Run artisan commands: ddev exec php artisan <command>
- Run migrations: ddev exec php artisan migrate
- Frontend build check: npm run build

## Architecture

- Backend is Laravel (controllers in app/Http/Controllers, models in app/Models, routes in routes/web.php and routes/api.php).
- Public payment API lives in routes/api.php and includes Midtrans transaction endpoints.
- Admin web + editor APIs are under /admin and /admin/api in routes/web.php.
- Invitation rendering is handled by app/Services/InvitationRenderer.php using Blade views in resources/views/templates/components.
- React invitation/template editor lives in resources/js/editor and is bundled through Vite entry resources/js/editor/main.jsx.

## Conventions and Domain Rules

- Respect multi-division data boundaries:
    - users.division drives access (for example super_admin checks in admin controllers)
    - business_unit segmentation is required for bookings/packages/galleries
- Do not remove or break Midtrans-related endpoints/integration unless explicitly requested.
- For invitation/template sections, preserve parent-child tree integrity:
    - parent_id and order_index must remain consistent
    - frontend store keeps both nested sections and flattened allSections for persistence
- For admin fetch requests, include CSRF token header (X-CSRF-TOKEN).
- When adding a new invitation section/component type, update all required layers together:
    - TypeScript types/schemas in resources/js/editor
    - backend persistence/serialization if needed
    - Blade component view in resources/views/templates/components

## Change Safety

- The repository often has in-progress local edits; never revert unrelated modifications.
- Keep diffs minimal and scoped to the requested task.
- Preserve existing route names, payload shapes, and status values unless change is requested.

## Useful References

- README.md for product and setup overview
- GEMINI.md for project-specific context and business conventions
- PLAN_UNDANGAN_DIGITAL.md for invitation roadmap context
