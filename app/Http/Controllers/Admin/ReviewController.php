<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('status', 'pending');
        $q = Review::orderByDesc('id');
        if (in_array($filter, ['pending', 'approved'], true)) {
            $q->where('status', $filter);
        } else {
            $filter = 'all';
        }

        return view('admin.reviews.index', [
            'reviews' => $q->get(),
            'filter'  => $filter,
            'counts'  => [
                'pending'  => Review::where('status', 'pending')->count(),
                'approved' => Review::where('status', 'approved')->count(),
            ],
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('ok', 'Komentar disetujui dan sekarang tampil di website.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('ok', 'Komentar dihapus.');
    }
}
