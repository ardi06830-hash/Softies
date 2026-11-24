@extends('admin.layout')
@section('title', 'Komentar')
@section('content')
<div class="bar"><h1>Komentar</h1><span class="small">Komentar pelanggan tampil di website setelah disetujui</span></div>

<div class="tabs">
    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="{{ $filter === 'pending' ? 'on' : '' }}">Menunggu ({{ $counts['pending'] }})</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="{{ $filter === 'approved' ? 'on' : '' }}">Disetujui ({{ $counts['approved'] }})</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" class="{{ $filter === 'all' ? 'on' : '' }}">Semua</a>
</div>

<div class="tbl">
<table>
    <tr><th>Komentar</th><th>Dari</th><th>Bintang</th><th>Status</th><th>Aksi</th></tr>
    @forelse ($reviews as $r)
        <tr>
            <td style="max-width:420px">{{ $r->body }}</td>
            <td>{{ $r->author }}</td>
            <td>{{ str_repeat('⭐', $r->stars) }}</td>
            <td>
                @if ($r->status === 'pending')<span class="badge st-menunggu">Menunggu</span>
                @else<span class="badge st-selesai">Tampil</span>@endif
            </td>
            <td class="actions">
                @if ($r->status === 'pending')
                    <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PATCH')<button class="btn green sm">Setujui</button></form>
                @endif
                <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus komentar ini?')">@csrf @method('DELETE')<button class="btn red sm">Hapus</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="small">Tidak ada komentar di kategori ini.</td></tr>
    @endforelse
</table>
</div>
@endsection
