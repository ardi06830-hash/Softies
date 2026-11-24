<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $upcoming = Order::whereNotIn('status', ['selesai', 'dibatalkan'])
            ->whereDate('pickup_date', '>=', Carbon::today('Asia/Jakarta')->toDateString())
            ->selectRaw('pickup_date, count(*) as orders, sum(total) as total')
            ->groupBy('pickup_date')->orderBy('pickup_date')->limit(6)->get();

        return view('admin.dashboard', [
            'newOrders'      => Order::whereNull('opened_at')->count(),
            'activeOrders'   => Order::whereIn('status', ['menunggu', 'diproses', 'siap_diambil'])->count(),
            'doneTotal'      => Order::where('status', 'selesai')->sum('total'),
            'pendingReviews' => Review::where('status', 'pending')->count(),
            'productCount'   => Product::count(),
            'upcoming'       => $upcoming,
            'latest'         => Order::with('items')->latest()->limit(6)->get(),
        ]);
    }
}
