# luminarabali.com - Project Context

## Project Overview
**luminarabali.com** is a robust backend microservice, booking, invoice, linktree, and digital invitation system designed for **Luminara Photobooth & Visual**. It serves five primary functions:
1.  **Payment Service**: Acts as a secure bridge between the local offline-first Flutter application and the **Midtrans Payment Gateway**, facilitating QRIS, VA, and E-Wallet payments within a LAN environment.
2.  **Booking System**: A web-based booking platform for customers to check availability and book photobooth/visual services, complete with an admin dashboard for management.
3.  **Invoice System**: Automated and manual invoice generation, management, and printing for client billing.
4.  **Linktree / Bio-Link System**: Multi-division bio link lists that support active/pinned status, custom order, and direct integration with bookings for instant photobooth download access.
5.  **Digital Invitation System**: A modular, React-powered visual editor that enables super admins to build templates and pages for digital event invitations with public RSVP management and asset library.

For a detailed breakdown of UI/UX design rules, aesthetic styles, color systems, and frontend consistency guidelines for AI Agents, please refer to the **[DESIGN.md](DESIGN.md)** document.

## Tech Stack
*   **Backend Framework**: Laravel 12 (PHP 8.2+)
*   **Database**: MySQL / MariaDB
*   **Frontend**: Blade Templates + Vite + Tailwind CSS v4 + React (used for the digital invitation visual editor)
*   **Payment Gateway**: Midtrans (Snap API)
*   **Image Processing**: Intervention Image v3 (`intervention/image` & `intervention/image-laravel` with WebP encoding & scaling support)
*   **API Client**: Google API Client (`google/apiclient` for Google Calendar Integration)
*   **Environment**: DDEV (Docker-based) or Native PHP
*   **API Authentication**: Laravel Sanctum

## Database Schema
The database supports a multi-division business model (`photobooth` & `visual`) as well as digital invitations and dynamic bio-links.

### Core Tables
1.  **Users (`users`)**
    *   `division`: Role/Access control (`super_admin`, `photobooth`, `visual`).
2.  **Packages (`packages` & `package_prices`)**
    *   `business_unit`: Links package to specific division (`photobooth` or `visual`).
    *   `type`: Unique identifier (e.g., `pb_unlimited`).
    *   `base_price`: Starting price.
    *   `prices` (Relation): One-to-many pricing tiers based on `duration_hours`.
3.  **Bookings (`bookings`)**
    *   `business_unit`: Inherited from selected package.
    *   `payment_proof`: Path to uploaded transfer proof.
    *   `dp_amount`: Recorded down payment.
    *   `event_maps_link`: Google Maps URL for the event.
    *   `link_drive`: Google Drive link to event photobooth photos.
    *   `thumbnail`: URL/path to processed event thumbnail (compressed as WebP).
4.  **Invoices (`invoices` & `invoice_items`)**
    *   Linked to `bookings` (optional for manual invoices).
    *   Financial tracking (`subtotal`, `tax_amount`, `discount_amount`, `grand_total`, `dp_amount`, `balance_due`).
    *   `status`: `PENDING`, `DP_BAYAR` (Partial), `LUNAS` (Paid).
5.  **Galleries (`galleries`)**
    *   `business_unit`: Segregates images by division.
    *   `is_featured`: Flags images for landing page hero sections.
6.  **Digital Invitation System Tables**
    *   `invitation_templates`: Stores global layout blueprints. Contains custom styles and `global_custom_css`.
    *   `invitation_pages`: Stores client invitation page details (bride/groom names, event date, published status, custom slug).
    *   `invitation_sections`: Modular nested sections composing the invitation (types like `hero`, `text`, `gallery`, `music`, `countdown`, `rsvp`, etc.). Stores settings inside a JSON column `props`.
    *   `invitation_assets`: Media library linking uploaded client assets to their page, storing mime-type, sizes, and dimensions.
    *   `invitation_rsvp_responses`: Records guest RSVPs (attendance status, names, comments, phone, emails).
7.  **Linktree Bio Links (`links`)**
    *   Stores navigation items for divisions.
    *   `business_unit`: Segregates link lists.
    *   `is_active` / `is_pinned`: Controls visibility and prioritization.
    *   `order` / `icon` / `thumbnail`: Visual properties.
8.  **Google Calendar Events (`google_calendar_events`)**
    *   Maps Laravel bookings to Google Calendar event IDs, tracking start/end times and status.

## Getting Started

### Prerequisites
*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   DDEV (Recommended)

### Installation (DDEV - Recommended)
1.  Start the DDEV environment:
    ```bash
    ddev start
    ```
2.  Install dependencies and setup the project:
    ```bash
    ddev composer setup
    ```
    *This script installs Composer dependencies, sets up `.env`, generates the app key, runs migrations, installs NPM packages, and builds assets.*

### Installation (Native)
1.  Install PHP dependencies:
    ```bash
    composer install
    ```
2.  Setup environment:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3.  Configure database in `.env`.
4.  Run migrations:
    ```bash
    php artisan migrate
    ```
5.  Install and build frontend assets:
    ```bash
    npm install && npm run build
    ```

## Development Workflow

### Running the Application
*   **DDEV**:
    ```bash
    ddev composer dev
    ```
    *Runs `php artisan serve`, queue listener, pail (logs), and `npm run dev` concurrently.*

*   **Native**:
    ```bash
    composer dev
    ```
    *Same as DDEV but runs on the host machine.*

### Running Tests
```bash
composer test
```
*Clears config and runs PHPUnit tests.*

## Project Structure
*   **`app/Models/`**: Core data models (`Booking`, `BlockedDate`, `User`, `Invoice`, `InvoiceItem`, `Gallery`, `Package`, `PackagePrice`, `Link`, `GoogleCalendarEvent`, `InvitationTemplate`, `InvitationPage`, `InvitationSection`, `InvitationAsset`, `InvitationRsvpResponse`, `Transaction`).
*   **`app/Services/`**:
    *   `GoogleCalendarService.php`: Integrates Google Client API for service account synchronization.
    *   `InvitationRenderer.php`: Renders complex template structures.
*   **`app/Http/Controllers/`**:
    *   `BookingController.php`: Handles public booking pages and admin dashboard logic.
    *   `LinktreeController.php`: Public bio-link renderer.
    *   `InvitationViewController.php`: Public digital invitations and guest RSVP submissions.
    *   `Admin/`:
        *   `InvoiceController.php` & `FinanceController.php`: Handles financial analytics, invoice management, and silent printing.
        *   `TemplateEditorController.php` & `InvitationEditorController.php`: Interfaces for the React visual editor.
        *   `LinkController.php`: Handles link CRUD and AJX reordering.
        *   `GalleryController.php` / `PackageController.php` / `UserController.php`: Standard dashboards.
    *   `Api/PaymentController.php`: Rest API transactions for Flutter integration.
*   **`app/Console/Commands/`**:
    *   `SyncBookingsToGoogleCalendar.php`: Command `calendar:sync` to manually reconcile bookings.
    *   `SyncInvoiceBookingPrices.php` / `SyncInvoiceBookingStatuses.php`: Utility commands to align invoices and booking records.
*   **`app/Listeners/`**:
    *   `SyncBookingToGoogleCalendar.php`: An asynchronous queued listener (`ShouldQueue`) responding to `BookingCreated` events.
*   **`routes/`**:
    *   `web.php`: Core UI paths, React visual editor endpoints, public linktree, public invitations, and admin scope.
    *   `api.php`: Bridge endpoints for offline Flutter terminal payments.
*   **`resources/views/`**: Blade templates for frontend and administrator views.
    *   `admin/finance/`: Financial overviews and reporting charts.
    *   `admin/links/`: Bio link lists with sortable features.
    *   `admin/templates/` & `admin/invitations/`: Drag-and-drop React integration.

## Key Features & Conventions

*   **Multi-Division Architecture**:
    *   **Data Segregation**: Resources are tagged with `business_unit` ('photobooth' or 'visual').
    *   **Access Control**: `super_admin` can manage all units. Division-specific users are strictly scoped to their division's records.
    *   **Frontend Routing**: Different public templates and landing paths exist for Photobooth vs Visual.

*   **Payment & Smart Sync**: Midtrans API handles automatic callback webhooks. Core commands exist to align statuses between booking flows and physical invoice statuses.

*   **Google Calendar Integration**: 
    *   Whenever a booking is completed, it fires a `BookingCreated` event.
    *   An asynchronous event listener `SyncBookingToGoogleCalendar` automatically pushes dates, event package scopes, event location maps, payment dues, and client information to the team calendar.

*   **Linktree Integration**:
    *   Division linktree bio-link pages (`/linkto/{division}`) dynamically serve configured navigation links.
    *   For the photobooth division, active bookings that contain Google Drive files (`link_drive`) are pulled and sorted into **Hari Ini** (today's events) and **Sebelumnya** (past events) directly on the landing page, offering quick, frictionless client access.

*   **Digital Invitation Editor**:
    *   Admin templates can be customized block-by-block using the React visual editor.
    *   Admins can adjust visibility, reorder blocks, load templates, duplicate templates, and upload page-specific assets.
    *   Guests view public pages, maps, check countdowns, and submit RSVPs dynamically.

*   **Finance & Reporting**:
    *   Tracks financial margins comparing `grand_total` bills against unpaid accounts receivable (`balance_due`).
    *   Interactive daily/monthly charts built into the admin console provide visual feedback on business performance.

*   **SweetAlert2 & Silent Printing**: 
    *   SweetAlert2 provides elegant state change notifications.
    *   Invoices support specialized, quiet printing configurations via print-friendly CSS.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.21
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version-specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

## PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== tailwindcss/core rules ===

## Tailwind CSS

- Use Tailwind CSS classes to style HTML; check and use existing Tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc.).
- Think through class placement, order, priority, and defaults. Remove redundant classes, add classes to parent or child carefully to limit repetition, and group elements logically.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing; don't use margins.

<code-snippet name="Valid Flex Gap Spacing Example" lang="html">
    <div class="flex gap-8">
        <div>Superior</div>
        <div>Michigan</div>
        <div>Erie</div>
    </div>
</code-snippet>

### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

=== tailwindcss/v4 rules ===

## Tailwind CSS 4

- Always use Tailwind CSS v4; do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.

<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>

### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option; use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
