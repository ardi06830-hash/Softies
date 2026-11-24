<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function payload(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category'       => 'required|in:cookie,brownie,scoop',
            'name'           => 'required|string|max:150',
            'subtitle'       => 'nullable|string|max:200',
            'description'    => 'required|string',
            'price'          => 'nullable|integer|min:0|max:10000000',
            'lead_days'      => 'required|integer|min:0|max:30',
            'badge'          => 'nullable|string|max:60',
            'image'          => 'nullable|image|max:4096',
            'emoji'          => 'nullable|string|max:20',
            'hero_name'      => 'nullable|string|max:150',
            'hero_desc'      => 'nullable|string|max:255',
            'featured_price' => 'nullable|string|max:40',
            'featured_order' => 'nullable|integer|min:0',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_premium']     = $request->boolean('is_premium');
        $data['featured_order'] = $data['featured_order'] ?? 0;
        $data['sort_order']     = $data['sort_order'] ?? ((int) Product::max('sort_order') + 1);
        unset($data['image']);
        $data['price_label'] = $this->label($data['price'] ?? null);

        if ($request->hasFile('image')) {
            if ($product?->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image') && $product?->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $data['image_path'] = null;
        }

        return $data;
    }

    private function label(?int $price): string
    {
        if ($price === null) return 'DM';
        return $price % 1000 === 0 ? ($price / 1000) . 'k' : number_format($price, 0, ',', '.');
    }

    public function index()
    {
        $products = Product::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['category' => 'cookie'])]);
    }

    public function store(Request $request)
    {
        Product::create($this->payload($request));
        return redirect()->route('admin.products.index')->with('ok', 'Menu berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->payload($request, $product));
        return redirect()->route('admin.products.index')->with('ok', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('ok', 'Menu dihapus.');
    }
}
