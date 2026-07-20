<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Satu query, statistik diturunkan dari koleksi yang sama: jumlahnya
        // kecil dan semuanya milik satu user, jadi tak perlu query terpisah
        // per angka.
        $orders = Order::with('template')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard.customer', [
            'totalOrders' => $orders->count(),
            'pendingCount' => $orders->where('status', Order::STATUS_PENDING)->count(),
            'paidCount' => $orders->where('status', Order::STATUS_PAID)->count(),
            'recentOrders' => $orders->take(4),
        ]);
    }
}
