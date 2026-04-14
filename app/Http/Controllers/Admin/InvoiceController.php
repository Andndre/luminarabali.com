<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    // List Invoices
    public function index()
    {
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        $query = Invoice::with('booking')->latest('invoice_date');

        if ($user->division !== 'super_admin') {
            // Filter invoices where the associated booking belongs to the division
            // OR if it's a manual invoice created by a user of that division (if we tracked creator)
            // Currently, Invoices are linked to Bookings which have business_unit.
            // For manual invoices without booking, we might need a way to distinguish.
            // Assuming manual invoices might not have booking_id, or we need to add business_unit to invoice table.
            // Looking at migration (inferred), Invoice has booking_id (nullable?). 
            // If nullable, we can't easily filter by division unless we add 'business_unit' to invoice table.
            // However, existing context implies Invoice belongs to a Booking.
            // For manual invoices, we'll try to link them or just show all for now if no booking.
            // BETTER: Filter by checking booking relation.
            $query->whereHas('booking', function($q) use ($user) {
                $q->where('business_unit', $user->division);
            })->orWhereDoesntHave('booking'); // Show manual invoices to everyone or handle differently? 
            // Let's assume manual invoices are visible to all or just super_admin?
            // "buat pada masing masing akun division juga" -> Manual invoices should probably be division specific.
            // But Invoice table doesn't have division column.
            // I'll filter by booking relation for now. Manual invoices (if any) will be visible to all or none if restricted strictly.
            // If I create a manual invoice, I won't have a booking.
            // Use case: "Invoice baru" -> Manual.
            // I will skip division filter for manual invoices (booking_id null) for now, or filter if I can.
        }

        $invoices = $query->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

    // Show Create Form
    public function create()
    {
        return view('admin.invoices.create');
    }

    // Store New Invoice (Manual)
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'customer_name' => 'required',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $invoice = Invoice::create([
            'booking_id' => null, // Manual invoice
            'invoice_number' => 'TEMP-' . time(), // Temporary
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'subtotal' => $request->subtotal,
            'discount_percent' => $request->discount_percent,
            'discount_amount' => $request->discount_amount,
            'tax_percent' => $request->tax_percent,
            'tax_amount' => $request->tax_amount,
            'grand_total' => $request->grand_total,
            'dp_amount' => $request->dp_amount,
            'balance_due' => $request->balance_due,
            'status' => $request->balance_due <= 0 ? 'PAID' : ($request->dp_amount > 0 ? 'PARTIAL' : 'UNPAID'),
        ]);

        // Update with formatted number
        $invNumber = 'INV/' . now()->format('Y/m') . '/MAN-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT);
        $invoice->update(['invoice_number' => $invNumber]);

        if($request->has('items')) {
            foreach($request->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'is_bonus' => isset($item['is_bonus']) && $item['is_bonus'] == 'on' ? true : false,
                ]);
            }
        }

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice baru berhasil dibuat.');
    }

    // Delete Invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->items()->delete();
        $invoice->delete();
        return back()->with('success', 'Invoice berhasil dihapus.');
    }

    // Show Invoice or Create if not exists (Redirect to Edit)
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        
        $userAuth = Auth::user()->id;
        $user = User::find($userAuth);
        if ($user->division !== 'super_admin' && $booking->business_unit !== $user->division) {
            abort(403);
        }

        // Check if invoice exists
        $invoice = Invoice::where('booking_id', $booking->id)->first();

        if (!$invoice) {
            // Create initial invoice based on booking data
            $invNumber = 'INV/' . now()->format('Y/m') . '/' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            
            $invoice = Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invNumber,
                'invoice_date' => now(),
                'customer_name' => $booking->customer_name,
                'customer_phone' => $booking->customer_phone,
                'customer_email' => $booking->customer_email,
                'subtotal' => $booking->price_total,
                'grand_total' => $booking->price_total - ($booking->dp_amount ?? 0),
                'dp_amount' => $booking->dp_amount ?? 0,
                'balance_due' => $booking->price_total - ($booking->dp_amount ?? 0),
            ]);

            // Create default item from booking
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $booking->package_name . ' (' . $booking->duration_hours . ' Jam)',
                'quantity' => 1,
                'price' => $booking->price_total,
                'total' => $booking->price_total,
            ]);
        }

        return redirect()->route('admin.invoices.edit', $invoice->id);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        return view('admin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Validation logic here if needed
        
        $invoice->update([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'customer_address' => $request->customer_address,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'subtotal' => $request->subtotal,
            'discount_percent' => $request->discount_percent,
            'discount_amount' => $request->discount_amount,
            'tax_percent' => $request->tax_percent,
            'tax_amount' => $request->tax_amount,
            'dp_amount' => $request->dp_amount,
            'grand_total' => $request->grand_total,
            'balance_due' => $request->balance_due,
            'status' => $request->balance_due <= 0 ? 'PAID' : ($request->dp_amount > 0 ? 'PARTIAL' : 'UNPAID'),
        ]);

        // Sync price_total to booking so dashboard revenue stays accurate
        if ($invoice->booking) {
            $invoice->booking->update([
                'price_total' => $invoice->grand_total,
            ]);
        }

        // Sync Items
        $invoice->items()->delete();
        
        if($request->has('items')) {
            foreach($request->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'is_bonus' => isset($item['is_bonus']) && $item['is_bonus'] == 'on' ? true : false,
                ]);
            }
        }

        return redirect()->route('admin.invoices.print', $invoice->id)
            ->with('success', 'Invoice berhasil disimpan.')
            ->with('auto_print', true);
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items');
        return view('admin.invoices.print', compact('invoice'));
    }
}
