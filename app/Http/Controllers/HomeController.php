<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'featured' => Product::where('is_featured', true)->orderBy('featured_order')->get(),
            'products' => Product::orderBy('sort_order')->orderBy('id')->get(),
            'reviews'  => Review::approved()->orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }
}
