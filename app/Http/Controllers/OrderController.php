<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\InvitationTemplate;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function store(string $slug)
    {
        $template = InvitationTemplate::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if ($template->price === null) {
            return redirect()->route('catalog.show', $slug)
                ->with('error', 'Desain ini belum berharga tetap. Hubungi kami untuk memesan.');
        }

        // Cegah order menganggur ganda: arahkan ke yang sudah ada.
        $existing = Order::where('user_id', Auth::id())
            ->where('invitation_template_id', $template->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_AWAITING])
            ->first();

        if ($existing) {
            return redirect()->route('orders.show', $existing);
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => Auth::id(),
            'invitation_template_id' => $template->id,
            'price' => $template->price,
            'status' => Order::STATUS_PENDING,
        ]);

        return redirect()->route('orders.show', $order);
    }

    public function index()
    {
        $orders = Order::with('template')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load('template');
        $bankAccounts = BankAccount::active()->get();

        return view('orders.show', compact('order', 'bankAccounts'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        Gate::authorize('update', $order);
        abort_unless($order->canUploadProof(), 403, 'Order ini tak bisa lagi menerima bukti.');

        $request->validate(['bukti' => 'required|image|max:5120']);

        if ($order->payment_proof_path && Storage::disk('local')->exists($order->payment_proof_path)) {
            Storage::disk('local')->delete($order->payment_proof_path);
        }

        $path = $request->file('bukti')->store('payment-proofs', 'local');

        $order->update([
            'payment_proof_path' => $path,
            'status' => Order::STATUS_AWAITING,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Bukti terunggah. Menunggu konfirmasi admin.');
    }

    public function showProof(Order $order)
    {
        Gate::authorize('view', $order);
        abort_unless($order->payment_proof_path, 404);

        return Storage::disk('local')->response($order->payment_proof_path);
    }
}
