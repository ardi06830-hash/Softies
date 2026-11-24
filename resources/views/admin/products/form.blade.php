@extends('admin.layout')
@section('title', $product->exists ? 'Edit Menu' : 'Tambah Menu')
@section('content')
<h1>{{ $product->exists ? 'Edit Menu' : 'Tambah Menu' }}</h1>
<form method="POST" class="box" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <label>Kategori
        <select name="category">
            @foreach (\App\Models\Product::CATEGORIES as $k => $c)
                <option value="{{ $k }}" @selected(old('category', $product->category) === $k)>{{ $c['emoji'] }} {{ $c['label'] }}</option>
            @endforeach
        </select>
    </label>

    <label>Gambar <span class="small">(jpg/png/webp, maks 4 MB. Kosong = tampil emoji sesuai kategori)</span>
        <input type="file" name="image" accept="image/*" id="imgInput">
    </label>
    <div id="imgPreview" style="margin:-6px 0 14px">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="" style="max-width:180px;border-radius:10px;display:block">
            <label style="font-weight:400;margin-top:8px"><input type="checkbox" name="remove_image" value="1"> Hapus gambar (kembali ke emoji)</label>
        @endif
    </div>

    <label>Nama <input type="text" name="name" value="{{ old('name', $product->name) }}" required></label>
    <label>Sub judul <span class="small">(mis. Red velvet w/ keju)</span>
        <input type="text" name="subtitle" value="{{ old('subtitle', $product->subtitle) }}"></label>
    <label>Deskripsi <textarea name="description" rows="3" required>{{ old('description', $product->description) }}</textarea></label>
    <label>Harga (Rupiah) <span class="small">(angka saja, mis. 5000. Kosongkan jika harga menyusul/DM)</span>
        <input type="number" name="price" min="0" step="500" value="{{ old('price', $product->price) }}"></label>
    <label>Minimal pesan berapa hari sebelum ambil <span class="small">(H-2 untuk menu biasa, H-5 untuk Birthday Brownie)</span>
        <input type="number" name="lead_days" min="0" max="30" value="{{ old('lead_days', $product->lead_days ?? 2) }}" required></label>
    <label>Badge <span class="small">(mis. 🔥 Bestseller, boleh kosong)</span>
        <input type="text" name="badge" value="{{ old('badge', $product->badge) }}"></label>
    <label style="font-weight:600">
        <input type="checkbox" name="is_premium" value="1" @checked(old('is_premium', $product->is_premium))> Beri border kuning (premium)
    </label>
    <label>Urutan di menu <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $product->sort_order) }}"></label>

    <hr style="border:0;border-top:1px dashed #ecd9c2;margin:18px 0">
    <b>Kartu di bagian Hero</b>
    <label style="font-weight:600;margin-top:10px">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Tampilkan di hero
    </label>
    <label>Emoji hero <input type="text" name="emoji" value="{{ old('emoji', $product->emoji) }}"></label>
    <label>Nama di hero <span class="small">(kosong = pakai nama menu)</span>
        <input type="text" name="hero_name" value="{{ old('hero_name', $product->hero_name) }}"></label>
    <label>Teks harga di hero <span class="small">(mis. Rp 5k)</span>
        <input type="text" name="featured_price" value="{{ old('featured_price', $product->featured_price) }}"></label>
    <label>Deskripsi singkat hero <span class="small">(opsional)</span>
        <input type="text" name="hero_desc" value="{{ old('hero_desc', $product->hero_desc) }}"></label>
    <label>Urutan di hero <span class="small">(kartu pertama tampil lebar)</span>
        <input type="number" name="featured_order" min="0" value="{{ old('featured_order', $product->featured_order ?? 0) }}"></label>

    <button class="btn" type="submit">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="btn gray">Batal</a>
</form>
<script>
document.getElementById('imgInput').addEventListener('change', function () {
    var f = this.files[0], box = document.getElementById('imgPreview');
    if (!f) return;
    box.innerHTML = '<img src="' + URL.createObjectURL(f) + '" style="max-width:180px;border-radius:10px;display:block">';
});
</script>
@endsection
