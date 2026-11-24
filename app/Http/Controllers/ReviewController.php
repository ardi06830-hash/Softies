<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // honeypot: bot biasanya mengisi kolom ini
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Terima kasih!']);
        }

        $data = $request->validate([
            'name'  => 'nullable|string|max:40',
            'stars' => 'required|integer|min:1|max:5',
            'body'  => 'required|string|min:5|max:500',
        ], [
            'body.required'  => 'Komentar wajib diisi.',
            'body.min'       => 'Komentar terlalu pendek.',
            'stars.required' => 'Pilih jumlah bintang.',
        ]);

        $name = trim((string) ($data['name'] ?? ''));

        Review::create([
            'body'       => $data['body'],
            'author'     => $name === '' ? 'Pelanggan' : $name . ' • Pelanggan',
            'stars'      => $data['stars'],
            'status'     => 'pending',
            'sort_order' => 0,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Terima kasih! Komentarmu akan tampil setelah disetujui admin. 🍪',
        ]);
    }
}
