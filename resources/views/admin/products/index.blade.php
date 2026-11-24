@extends('admin.layout')
@section('title', 'Kelola Menu')
@section('content')
<div class="bar">
    <h1>Kelola Menu</h1>
    <a href="{{ route('admin.products.create') }}" class="btn">+ Tambah Menu</a>
</div>
<div class="tbl">
<table>
    <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Badge</th><th>Hero</th><th>Urutan</th><th>Aksi</th></tr>
    @forelse ($products as $p)
        <tr>
            <td style="width:70px">
                @if ($p->image_url)
                    <img src="{{ $p->image_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px">
                @else
                    <span style="font-size:2rem">{{ $p->category_emoji }}</span>
                @endif
            </td>
            <td><b>{{ $p->name }}</b><br><span class="small">{{ $p->subtitle }}</span></td>
            <td>{{ $p->category_label }}</td>
            <td>{{ $p->price_label }}</td>
            <td>{{ $p->badge }}</td>
            <td>{{ $p->is_featured ? '✔ ('.$p->featured_order.')' : '-' }}</td>
            <td>{{ $p->sort_order }}</td>
            <td class="actions">
                <a href="{{ route('admin.products.edit', $p) }}" class="btn sm">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Hapus menu ini?')">
                    @csrf @method('DELETE')
                    <button class="btn sm red">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="8">Belum ada menu.</td></tr>
    @endforelse
</table>
</div>
@endsection
