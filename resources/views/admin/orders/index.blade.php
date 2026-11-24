@extends('admin.layout')
@section('title', 'Pesanan')
@section('content')
<div class="bar"><h1>Pesanan</h1><span class="small">Klik baris untuk melihat isi pesanan dan mengubah status</span></div>

<form method="GET" class="filters">
    <div>
        <label>Status</label>
        <select name="status">
            <option value="">Semua</option>
            <option value="baru" @selected(request('status') === 'baru')>Baru (belum dibuka)</option>
            @foreach (\App\Models\Order::STATUSES as $k => $v)
                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Tanggal ambil dari</label><input type="date" name="from" value="{{ request('from') }}"></div>
    <div><label>sampai</label><input type="date" name="to" value="{{ request('to') }}"></div>
    <button class="btn" type="submit">Terapkan</button>
    <a class="btn gray" href="{{ route('admin.orders.index') }}">Reset</a>
</form>

<div class="tbl">
<table>
    <tr><th>#</th><th>Nama</th><th>No. WA</th><th>Pesanan</th><th>Jml</th><th>Total</th><th>Tgl ambil</th><th>Status</th><th>Catatan</th></tr>
    @forelse ($orders as $o)
        <tr class="row-click {{ $o->is_new ? 'is-new' : '' }}" data-id="{{ $o->id }}" data-order="{{ json_encode($payloads[$o->id]) }}">
            <td>{{ $o->id }}</td>
            <td><span class="new-tag">@if($o->is_new)<span class="tag-new">BARU</span>@endif</span><b>{{ $o->name }}</b></td>
            <td>{{ $o->whatsapp }}</td>
            <td style="max-width:260px">{{ \Illuminate\Support\Str::limit($o->summary, 70) }}</td>
            <td>{{ $o->item_count ?: '-' }}</td>
            <td>{{ \App\Models\Order::rupiah($o->total) }}{{ $o->has_custom ? ' +' : '' }}</td>
            <td>{{ $o->pickup_date ? $o->pickup_date->format('d/m/Y') : '-' }}</td>
            <td><span class="badge st-{{ $o->status }}" data-status-badge>{{ $o->status_label }}</span></td>
            <td class="small" style="max-width:180px">{{ \Illuminate\Support\Str::limit($o->notes ?: '-', 50) }}</td>
        </tr>
    @empty
        <tr><td colspan="9" class="small">Belum ada pesanan{{ request()->hasAny(['status','from','to']) ? ' untuk filter ini' : '' }}.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:14px">{{ $orders->links() }}</div>

{{-- POPUP ISI PESANAN --}}
<div class="modal" id="orderModal">
    <div class="modal-box">
        <button type="button" class="modal-x" id="omClose" aria-label="Tutup">✕</button>
        <h1 id="omTitle"></h1>
        <dl class="kv">
            <dt>Nama</dt><dd id="omName"></dd>
            <dt>WhatsApp</dt><dd><span id="omWa"></span> <a id="omWaLink" class="btn green sm" target="_blank">Chat WA</a></dd>
            <dt>Tanggal ambil</dt><dd id="omPickup"></dd>
            <dt>Metode</dt><dd id="omMethod"></dd>
            <dt>Alamat</dt><dd id="omAddress"></dd>
            <dt>Catatan</dt><dd id="omNotes"></dd>
            <dt>Dibuat</dt><dd id="omCreated"></dd>
        </dl>

        <div id="omLegacy" class="small" style="background:#FFFBE6;padding:10px;border-radius:8px;margin-bottom:10px;display:none"></div>
        <table id="omItemsTable">
            <tr><th>Menu</th><th>Jml</th><th>Harga</th><th>Subtotal</th></tr>
            <tbody id="omItems"></tbody>
        </table>
        <p style="text-align:right;margin:10px 0 18px"><b>Total: <span id="omTotal"></span></b><span id="omCustom" class="small"></span></p>

        <form method="POST" id="omForm">
            @csrf @method('PATCH')
            <label>Status pesanan
                <select name="status" id="omStatus">
                    @foreach (\App\Models\Order::STATUSES as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </label>
            <button class="btn" type="submit">Simpan status</button>
            <button class="btn gray" type="button" id="omCancel">Tutup</button>
        </form>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('orderModal');
    var $ = function (id) { return document.getElementById(id); };
    var token = document.querySelector('#omForm input[name=_token]').value;

    function close() { modal.classList.remove('open'); }
    $('omClose').onclick = close; $('omCancel').onclick = close;
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

    document.querySelectorAll('tr.row-click').forEach(function (row) {
        row.addEventListener('click', function () {
            var o = JSON.parse(row.dataset.order);
            $('omTitle').textContent = 'Pesanan ' + o.code;
            $('omName').textContent = o.name;
            $('omWa').textContent = o.whatsapp;
            if (o.wa_link) { $('omWaLink').href = o.wa_link; $('omWaLink').style.display = ''; } else { $('omWaLink').style.display = 'none'; }
            $('omPickup').textContent = o.pickup;
            $('omMethod').textContent = o.method;
            $('omAddress').textContent = o.address || '-';
            $('omNotes').textContent = o.notes || '-';
            $('omCreated').textContent = o.created || '-';
            $('omTotal').textContent = o.total;
            $('omCustom').textContent = o.has_custom ? '  (+ item harga menyusul)' : '';

            var body = $('omItems'); body.innerHTML = '';
            o.items.forEach(function (i) {
                var tr = document.createElement('tr');
                [i.name, i.qty + 'x', i.price, i.subtotal].forEach(function (v) {
                    var td = document.createElement('td'); td.textContent = v; tr.appendChild(td);
                });
                body.appendChild(tr);
            });
            $('omItemsTable').style.display = o.items.length ? '' : 'none';
            var lg = $('omLegacy');
            if (o.legacy) { lg.style.display = 'block'; lg.textContent = 'Pesanan lama (teks bebas): ' + o.legacy; } else { lg.style.display = 'none'; }

            $('omStatus').value = o.status;
            $('omForm').action = o.update_url;
            modal.classList.add('open');

            // tandai sudah dibuka
            if (o.is_new) {
                fetch(o.open_url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(function () {
                        o.is_new = false;
                        row.dataset.order = JSON.stringify(o);
                        row.classList.remove('is-new');
                        var t = row.querySelector('.tag-new'); if (t) t.remove();
                    }).catch(function () {});
            }
        });
    });
})();
</script>
@endsection
