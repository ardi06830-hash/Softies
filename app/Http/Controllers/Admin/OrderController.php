<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::with('items')->orderByRaw('opened_at IS NULL DESC')->latest();

        $status = $request->query('status');
        if ($status === 'baru') {
            $q->whereNull('opened_at');
        } elseif (array_key_exists((string) $status, Order::STATUSES)) {
            $q->where('status', $status);
        }
        if ($request->filled('from')) $q->whereDate('pickup_date', '>=', $request->query('from'));
        if ($request->filled('to'))   $q->whereDate('pickup_date', '<=', $request->query('to'));

        $orders   = $q->paginate(15)->withQueryString();
        $payloads = $orders->mapWithKeys(fn ($o) => [$o->id => $this->payload($o)]);

        return view('admin.orders.index', compact('orders', 'payloads'));
    }

    public function open(Order $order)
    {
        if ($order->opened_at === null) {
            $order->update(['opened_at' => now()]);
        }
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:' . implode(',', array_keys(Order::STATUSES))]);
        $order->update($data + ['opened_at' => $order->opened_at ?? now()]);
        return back()->with('ok', 'Status pesanan #' . $order->id . ' diubah menjadi "' . $order->status_label . '".');
    }

    private function payload(Order $o): array
    {
        return [
            'id'         => $o->id,
            'code'       => '#' . $o->id,
            'name'       => $o->name,
            'whatsapp'   => $o->whatsapp,
            'wa_link'    => $o->wa_link,
            'pickup'     => $o->pickup_label,
            'method'     => $o->delivery_method,
            'address'    => $o->address,
            'notes'      => $o->notes,
            'status'     => $o->status,
            'total'      => Order::rupiah($o->total),
            'has_custom' => $o->has_custom,
            'created'    => optional($o->created_at)->format('d/m/Y H:i'),
            'is_new'     => $o->is_new,
            'open_url'   => route('admin.orders.open', $o),
            'update_url' => route('admin.orders.update', $o),
            'items'      => $o->items->map(fn ($i) => [
                'name'     => $i->name,
                'qty'      => $i->qty,
                'price'    => $i->price === null ? 'Harga menyusul' : Order::rupiah($i->price),
                'subtotal' => $i->price === null ? '-' : Order::rupiah($i->subtotal),
            ])->all(),
            'legacy'     => $o->items->isEmpty() ? trim(($o->product_type ? '[' . $o->product_type . '] ' : '') . $o->flavors) : null,
        ];
    }
}
