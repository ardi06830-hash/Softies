<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportProductImages extends Command
{
    protected $signature = 'softies:import-images {file : Path ke file HTML website lama}';
    protected $description = 'Ambil gambar base64 dari HTML lama, simpan ke storage, dan pasang ke produk';

    private function norm(string $s): string
    {
        $s = preg_replace('/\(.*?\)/', '', $s);
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $s));
    }

    public function handle(): int
    {
        $file = $this->argument('file');
        if (!is_file($file)) {
            $this->error("File tidak ditemukan: $file");
            return self::FAILURE;
        }

        $html = file_get_contents($file);
        preg_match_all('/<img src="data:image\/(\w+);base64,([^"]+)"\s+alt="([^"]*)"/s', $html, $m, PREG_SET_ORDER);

        $products = Product::all();
        $done = 0;

        foreach ($m as [, $ext, $b64, $alt]) {
            $product = $products->first(fn ($p) => $this->norm($p->name) === $this->norm($alt));
            if (!$product) {
                $this->warn("Tidak ada produk untuk gambar: $alt");
                continue;
            }
            $ext = $ext === 'jpeg' ? 'jpg' : $ext;
            $path = 'products/' . $product->id . '-' . substr(md5($alt), 0, 6) . '.' . $ext;
            Storage::disk('public')->put($path, base64_decode($b64));
            $product->update(['image_path' => $path]);
            $this->info("OK: {$product->name}");
            $done++;
        }

        $this->info("Selesai, $done gambar diimpor.");
        return self::SUCCESS;
    }
}
