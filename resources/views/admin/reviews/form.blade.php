@extends('admin.layout')
@section('title', $review->exists ? 'Edit Komentar' : 'Tambah Komentar')
@section('content')
<h1>{{ $review->exists ? 'Edit Komentar' : 'Tambah Komentar' }}</h1>
<form method="POST" class="box"
      action="{{ $review->exists ? route('admin.reviews.update', $review) : route('admin.reviews.store') }}">
    @csrf
    @if ($review->exists) @method('PUT') @endif

    <label>Isi komentar <textarea name="body" rows="4" required>{{ old('body', $review->body) }}</textarea></label>
    <label>Dari <input type="text" name="author" value="{{ old('author', $review->author) }}" required></label>
    <label>Bintang
        <select name="stars">
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected((int) old('stars', $review->stars) === $i)>{{ $i }}</option>
            @endfor
        </select>
    </label>
    <label>Urutan <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $review->sort_order ?? 0) }}"></label>

    <button class="btn" type="submit">Simpan</button>
    <a href="{{ route('admin.reviews.index') }}" class="btn gray">Batal</a>
</form>
@endsection
