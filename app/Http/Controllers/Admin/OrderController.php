<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['template', 'user'])
            ->when(in_array($status, Order::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['template', 'user', 'confirmedBy']);

        return view('admin.orders.show', compact('order'));
    }

    public function confirm(Order $order)
    {
        $this->authorizeFinancialAction();

        abort_if(
            in_array($order->status, [Order::STATUS_PAID, Order::STATUS_CANCELLED], true),
            403, 'Order sudah final.'
        );

        $order->update([
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
            'confirmed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Order dikonfirmasi lunas.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeFinancialAction();

        abort_if($order->status === Order::STATUS_PAID, 403, 'Order lunas tak bisa dibatalkan.');

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Order dibatalkan.');
    }

    /**
     * Konfirmasi/batal = aksi finansial atas data customer, hanya super_admin.
     * Middleware `staff` cuma memblokir customer; designer lolos, padahal
     * designer tak boleh menyentuh data customer (lihat User::canDesignTemplates
     * doc + UserController/InvoiceController yang super_admin-only). Index/show
     * tetap terbuka untuk semua staf sesuai spec "staf boleh lihat semua".
     */
    private function authorizeFinancialAction(): void
    {
        abort_unless(Auth::user()?->division === 'super_admin', 403, 'Hanya super admin.');
    }
}
