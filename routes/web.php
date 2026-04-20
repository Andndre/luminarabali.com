<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;

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
    Route::get('/templates/test', function () { return view('admin.templates.test'); })->name('admin.templates.test');
    Route::get('/templates/{id}/editor', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'editor'])->name('admin.templates.editor');
    Route::get('/templates/{id}/editor-react', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'editorReact'])->name('admin.templates.editor-react');
    Route::get('/templates/{id}/preview', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'preview'])->name('admin.templates.preview');
    Route::post('/templates/{id}/publish', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'publish'])->name('admin.templates.publish');

    // Invitations Routes
    Route::resource('invitations', \App\Http\Controllers\Admin\InvitationController::class)->names('admin.invitations');
    Route::get('/invitations/{id}/editor', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'editor'])->name('admin.invitations.editor');
    Route::post('/invitations/{id}/publish', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'publish'])->name('admin.invitations.publish');

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
        Route::delete('/templates/sections/{id}', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'deleteSection'])->name('templates.sections.delete');
        Route::post('/templates/sections/reorder', [\App\Http\Controllers\Admin\TemplateEditorController::class, 'reorderSections'])->name('templates.sections.reorder');

        // Invitations API
        Route::get('/invitations/{id}/load', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'load']);

        // Sections API (for invitations)
        Route::post('/sections', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'saveSection']);
        Route::put('/sections/{id}', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'updateSection']);
        Route::delete('/sections/{id}', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'deleteSection']);
        Route::post('/sections/reorder', [\App\Http\Controllers\Admin\InvitationEditorController::class, 'reorderSections']);

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
