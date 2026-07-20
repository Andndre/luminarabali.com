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
        abort_if($order->status === Order::STATUS_PAID, 403, 'Order lunas tak bisa dibatalkan.');

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Order dibatalkan.');
    }
}
