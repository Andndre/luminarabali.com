<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Public Routes - Gate & Divisions
Route::get('/', [BookingController::class, 'landing'])->name('home');
Route::get('/photobooth', [BookingController::class, 'photoboothLanding'])->name('photobooth.home');
Route::get('/visual', [BookingController::class, 'visualLanding'])->name('visual.home');

Route::get('/pricelist', [BookingController::class, 'pricelistPhotobooth'])->name('pricelist');
Route::get('/pricelist/visual', [BookingController::class, 'pricelistVisual'])->name('pricelist.visual');

Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/calendar/availability', [BookingController::class, 'availability'])->name('calendar.availability');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [BookingController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'adminCreate'])->name('admin.bookings.create');
    Route::post('/bookings', [BookingController::class, 'adminStore'])->name('admin.bookings.store');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'adminEdit'])->name('admin.bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'adminUpdate'])->name('admin.bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'adminDestroy'])->name('admin.bookings.destroy');
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.update-status');

    // Invoice Management Routes
    Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('/invoices/create', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('admin.invoices.create');
    Route::post('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('admin.invoices.store');
    Route::delete('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('admin.invoices.destroy');

    // Invoice Detail Routes
    Route::get('/bookings/{id}/invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.bookings.invoice');
    Route::get('/invoices/{invoice}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('admin.invoices.edit');
    Route::put('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('admin.invoices.update');
    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('admin.invoices.print');
    Route::post('/invoices/{invoice}/mark-as-paid', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsPaid'])->name('admin.invoices.markAsPaid');
    Route::post('/invoices/{invoice}/mark-as-dp', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsDp'])->name('admin.invoices.markAsDp');

    // Finance Route
    Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');

    // Calendar Routes
    Route::get('/calendar', [BookingController::class, 'calendarIndex'])->name('admin.calendar.index');
    Route::post('/calendar/block', [BookingController::class, 'blockDate'])->name('admin.calendar.block');
    Route::delete('/calendar/{id}', [BookingController::class, 'unblockDate'])->name('admin.calendar.unblock');

    // Package Routes
    Route::resource('packages', \App\Http\Controllers\Admin\PackageController::class)->names('admin.packages');

    // User Management Routes (Super Admin Only logic handled in controller)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');

    // Gallery Routes
    Route::post('/galleries/{gallery}/toggle-featured', [\App\Http\Controllers\Admin\GalleryController::class, 'toggleFeatured'])->name('admin.galleries.toggle-featured');
    Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class)->names('admin.galleries');

    // Invitation Templates Routes
    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->names('admin.templates');
    Route::post('/templates/{id}/duplicate', [\App\Http\Controllers\Admin\TemplateController::class, 'duplicate'])->name('admin.templates.duplicate');
    Route::get('/templates/test', function () {
        return view('admin.templates.test');
    })->name('admin.templates.test');
    Route::get('/templates/{id}/preview', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'preview'])->name('admin.templates.preview');
    Route::post('/templates/{id}/publish', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'publish'])->name('admin.templates.publish');
    Route::get('/templates/{id}/studio', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'studio'])->name('admin.templates.studio');
    Route::get('/templates/{id}/studio/preview', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'studioPreview'])->name('admin.templates.studio.preview');

    // Invitations Routes
    Route::get('/invitations/{id}/preview', [\App\Http\Controllers\Admin\InvitationCustomizerController::class, 'preview'])->name('admin.invitations.customizer-preview');
    Route::get('/invitations/{id}/customizer', [\App\Http\Controllers\Admin\InvitationCustomizerController::class, 'show'])->name('admin.invitations.customizer');
    // Layar editor per-undangan lama digantikan customizer (spec fase 2 §8).
    Route::get('/invitations/{id}/editor', fn ($id) => redirect()->route('admin.invitations.customizer', $id))->name('admin.invitations.editor');
    Route::resource('invitations', \App\Http\Controllers\Admin\InvitationController::class)->names('admin.invitations');

    // Media Library Routes
    Route::get('/assets', [\App\Http\Controllers\Admin\InvitationAssetController::class, 'indexView'])->name('admin.assets.index');

    // Links Management Routes
    Route::resource('links', \App\Http\Controllers\Admin\LinkController::class)->names('admin.links');
    Route::post('links/reorder', [\App\Http\Controllers\Admin\LinkController::class, 'reorder'])->name('admin.links.reorder');

    // API Routes for Visual Editor
    Route::prefix('api')->name('api.')->group(function () {
        // Templates API
        Route::get('/templates/{id}/load', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'load']);
        Route::post('/templates/sections', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'saveSection'])->name('templates.sections.save');
        Route::put('/templates/sections/{id}', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'updateSection'])->name('templates.sections.update');
        Route::delete('/templates/sections/{id}', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'deleteSection'])->name('templates.sections.delete');
        Route::post('/templates/sections/reorder', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'reorderSections'])->name('templates.sections.reorder');

        Route::prefix('studio')->name('studio.')->group(function () {
            Route::post('/templates/{templateId}/sections', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'storeSection'])
                ->name('templates.sections.store');
            Route::post('/render-section', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'renderSection'])
                ->name('render-section');
            Route::patch('/templates/{templateId}/theme', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'updateTheme'])
                ->name('templates.theme.update');
            Route::post('/sections/{id}/duplicate', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'duplicateSection'])
                ->name('sections.duplicate');
            Route::get('/presets', [\App\Http\Controllers\Admin\DesignPresetController::class, 'index'])->name('presets.index');
            Route::post('/presets', [\App\Http\Controllers\Admin\DesignPresetController::class, 'store'])->name('presets.store');
            Route::delete('/presets/{id}', [\App\Http\Controllers\Admin\DesignPresetController::class, 'destroy'])->name('presets.destroy');
        });

        // Invitations API
        Route::get('/invitations/{id}/load', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'load']);
        Route::get('/invitations/{id}/customizer', [\App\Http\Controllers\Admin\InvitationCustomizerController::class, 'load']);
        Route::put('/invitations/{id}/customizer', [\App\Http\Controllers\Admin\InvitationCustomizerController::class, 'save']);

        // Sections API (for invitations)
        Route::post('/sections', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'saveSection']);
        Route::put('/sections/{id}', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'updateSection']);
        Route::delete('/sections/{id}', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'deleteSection']);
        Route::post('/sections/reorder', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'reorderSections']);

        // RSVP moderation (section wishes)
        Route::patch('/rsvp/{id}/toggle-hidden', [\App\Http\Controllers\Admin\InvitationController::class, 'toggleRsvpHidden'])->name('rsvp.toggle-hidden');

        // Assets API
        Route::get('/assets', [\App\Http\Controllers\Admin\InvitationAssetController::class, 'index']);
        Route::post('/assets/upload', [\App\Http\Controllers\Admin\InvitationAssetController::class, 'upload']);
        Route::delete('/assets/{id}', [\App\Http\Controllers\Admin\InvitationAssetController::class, 'destroy']);
        Route::put('/assets/{id}', [\App\Http\Controllers\Admin\InvitationAssetController::class, 'update']);
    });
});

// Legacy Midtrans Routes
Route::get('/payment-finish', function () {
    return view('payment_finish');
});
Route::get('/payment-failed', function () {
    return view('payment_failed');
});

// Public Invitation Routes
Route::get('/invitation/{slug}', [\App\Http\Controllers\InvitationViewController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{slug}/rsvp', [\App\Http\Controllers\InvitationViewController::class, 'rsvp'])->name('invitation.rsvp');

// Public Linktree Routes
Route::get('/linkto/{division}', [\App\Http\Controllers\LinktreeController::class, 'show'])->name('linktree.show');
Route::get('/gallery/drive/{folderId}', [\App\Http\Controllers\LinktreeController::class, 'driveGallery'])->name('gallery.drive');

Route::get('/temp-login', function () {
    abort_unless(app()->environment('local'), 404);
    auth()->login(\App\Models\User::first());

    return redirect()->route('admin.dashboard');
});
