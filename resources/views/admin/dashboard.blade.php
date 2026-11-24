@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<div class="bar"><h1>Dashboard</h1><span class="small">Ringkasan hari ini</span></div>

<div class="cards">
    <a class="stat hot" href="{{ route('admin.orders.index', ['status' => 'baru']) }}"><b>{{ $newOrders }}</b><span>Pesanan baru (belum dibuka)</span></a>
    <a class="stat gold" href="{{ route('admin.orders.index') }}"><b>{{ $activeOrders }}</b><span>Pesanan aktif (belum selesai)</span></a>
    <a class="stat green" href="{{ route('admin.orders.index', ['status' => 'selesai']) }}"><b style="font-size:1.6rem;padding-top:6px">{{ \App\Models\Order::rupiah($doneTotal) }}</b><span>Total pesanan sudah diambil</span></a>
    <a class="stat hot" href="{{ route('admin.reviews.index') }}"><b>{{ $pendingReviews }}</b><span>Komentar menunggu persetujuan</span></a>
    <a class="stat" href="{{ route('admin.products.index') }}"><b>{{ $productCount }}</b><span>Jumlah menu</span></a>
</div>

<div class="grid2">
    <div class="panel">
        <h2>Jadwal ambil mendatang (Sabtu/Minggu)</h2>
        <table>
            <tr><th>Tanggal</th><th>Pesanan</th><th>Total</th></tr>
            @forelse ($upcoming as $u)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($u->pickup_date)->locale('id')->translatedFormat('l, d F Y') }}</td>
                    <td>{{ $u->orders }}</td>
                    <td>{{ \App\Models\Order::rupiah($u->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="small">Belum ada jadwal ambil.</td></tr>
            @endforelse
        </table>
    </div>

    <div class="panel">
        <h2>Pesanan terbaru</h2>
        <table>
            <tr><th>Nama</th><th>Pesanan</th><th>Status</th></tr>
            @forelse ($latest as $o)
                <tr>
                    <td>@if($o->is_new)<span class="tag-new">BARU</span>@endif<a href="{{ route('admin.orders.index') }}"><b>{{ $o->name }}</b></a></td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($o->summary, 40) }}</td>
                    <td><span class="badge st-{{ $o->status }}">{{ $o->status_label }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" class="small">Belum ada pesanan.</td></tr>
            @endforelse
        </table>
        <p style="margin-top:12px"><a class="btn sm" href="{{ route('admin.orders.index') }}">Lihat semua pesanan →</a></p>
    </div>
</div>
@endsection
