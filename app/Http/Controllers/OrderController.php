<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const WA = '6285606685163';
    private const METHODS = ['Walk-in Pickup', 'Local Delivery (Kediri)', 'Shopee Shipping (Brownies only)'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'whatsapp'        => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'pickup_date'     => 'required|date',
            'delivery_method' => ['required', Rule::in(self::METHODS)],
            'address'         => 'nullable|string|max:500',
            'notes'           => 'nullable|string|max:1000',
            'items'           => 'required|array|min:1|max:30',
            'items.*.id'      => 'required|integer|exists:products,id',
            'items.*.qty'     => 'required|integer|min:1|max:99',
        ], [
            'name.required'        => 'Nama wajib diisi.',
            'whatsapp.required'    => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex'       => 'Nomor WhatsApp tidak valid.',
            'pickup_date.required' => 'Tanggal ambil wajib dipilih.',
            'items.required'       => 'Keranjang masih kosong, pilih menu dulu.',
            'items.min'            => 'Keranjang masih kosong, pilih menu dulu.',
        ]);

        // gabungkan item duplikat, ambil harga asli dari database
        $qtyById = [];
        foreach ($data['items'] as $it) {
            $qtyById[$it['id']] = ($qtyById[$it['id']] ?? 0) + $it['qty'];
        }
        $products = Product::whereIn('id', array_keys($qtyById))->get()->keyBy('id');

        // ---- aturan tanggal ambil: hanya Sabtu/Minggu, minimal H-n ----
        $lead   = (int) $products->max('lead_days');
        $pickup = Carbon::parse($data['pickup_date'], 'Asia/Jakarta')->startOfDay();
        $min    = Carbon::today('Asia/Jakarta')->addDays($lead);

        if (!$pickup->isWeekend()) {
            throw ValidationException::withMessages(['pickup_date' => 'Pengambilan pesanan hanya bisa hari Sabtu atau Minggu.']);
        }
        if ($pickup->lt($min)) {
            $why = $lead > 2 ? ' (ada menu yang butuh pemesanan H-' . $lead . ', mis. Birthday Brownie)' : '';
            throw ValidationException::withMessages(['pickup_date' => 'Pesanan minimal dibuat H-' . $lead . ' sebelum tanggal ambil' . $why . '. Pilih tanggal ' . $min->locale('id')->translatedFormat('d F Y') . ' atau setelahnya.']);
        }

        // ---- aturan metode ----
        $method = $data['delivery_method'];
        if ($method !== 'Walk-in Pickup' && blank($data['address'] ?? null)) {
            throw ValidationException::withMessages(['address' => 'Alamat wajib diisi untuk metode ini.']);
        }
        if ($method === 'Shopee Shipping (Brownies only)' && $products->contains(fn ($p) => $p->category !== 'brownie')) {
            throw ValidationException::withMessages(['delivery_method' => 'Pengiriman Shopee hanya untuk brownies. Ubah metode atau hapus cookies dari keranjang.']);
        }

        $order = DB::transaction(function () use ($data, $qtyById, $products, $pickup) {
            $total = 0;
            $rows  = [];
            foreach ($qtyById as $id => $qty) {
                $p = $products[$id];
                $sub = $p->price !== null ? $p->price * $qty : 0;
                $total += $sub;
                $rows[] = ['product_id' => $p->id, 'name' => $p->name, 'price' => $p->price, 'qty' => $qty, 'subtotal' => $sub];
            }

            $order = Order::create([
                'name'            => $data['name'],
                'whatsapp'        => $data['whatsapp'],
                'product_type'    => $products->pluck('category_label')->unique()->implode(', '),
                'pickup_date'     => $pickup->toDateString(),
                'delivery_method' => $data['delivery_method'],
                'address'         => $data['address'] ?? null,
                'notes'           => $data['notes'] ?? null,
                'status'          => 'menunggu',
                'total'           => $total,
            ]);
            $order->items()->createMany($rows);
            return $order->load('items');
        });

        return response()->json(['ok' => true, 'code' => '#' . $order->id, 'wa_url' => $this->waUrl($order)]);
    }

    private function waUrl(Order $o): string
    {
        $lines = $o->items->map(function ($i) {
            $price = $i->price === null ? 'harga menyusul' : Order::rupiah($i->subtotal);
            return "- {$i->qty}x {$i->name} ({$price})";
        })->implode("\n");

        $total = Order::rupiah($o->total) . ($o->has_custom ? ' + item harga menyusul' : '');

        $msg = "Hii kak! Mau order nih 🍪\n\n"
            . "Kode pesanan: #{$o->id}\n"
            . "Nama: {$o->name}\n"
            . "WA: {$o->whatsapp}\n\n"
            . "Pesanan:\n{$lines}\n"
            . "Total: {$total}\n\n"
            . "Tanggal ambil: {$o->pickup_label}\n"
            . "Metode: {$o->delivery_method}\n"
            . 'Alamat: ' . ($o->address ?: '-') . "\n"
            . 'Notes: ' . ($o->notes ?: '-');

        return 'https://wa.me/' . self::WA . '?text=' . rawurlencode($msg);
    }
}
