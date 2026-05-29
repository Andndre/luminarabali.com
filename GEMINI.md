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
