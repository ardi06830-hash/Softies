(function () {
    var $ = function (id) { return document.getElementById(id); };
    var rupiah = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
    var token = document.querySelector('meta[name="csrf-token"]').content;

    // ---------- Toast ----------
    var toastTimer;
    function toast(msg) {
        var t = $('toast'); t.textContent = msg; t.classList.add('show');
        clearTimeout(toastTimer); toastTimer = setTimeout(function () { t.classList.remove('show'); }, 2200);
    }

    // ---------- Filter kategori ----------
    var tabs = document.querySelectorAll('.tab-btn');
    var cards = document.querySelectorAll('#productsGrid .product-card');
    var note = $('brownieNote'), empty = $('emptyNote'), grid = $('productsGrid');
    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            tabs.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var f = btn.dataset.filter, shown = 0;
            cards.forEach(function (c) {
                var ok = f === 'all' || c.dataset.category === f;
                c.classList.toggle('hidden', !ok);
                if (ok) { c.classList.add('visible'); shown++; }
            });
            note.classList.toggle('hidden', f !== 'brownie');
            empty.classList.toggle('hidden', shown > 0);
            grid.classList.toggle('hidden', shown === 0);
        });
    });

    // ---------- Keranjang (disimpan di browser) ----------
    var KEY = 'softies_cart_v1';
    var cart = [];
    try { cart = JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { cart = []; }
    function save() { try { localStorage.setItem(KEY, JSON.stringify(cart)); } catch (e) {} }

    function addToCart(card) {
        var d = card.dataset, id = parseInt(d.id, 10);
        var found = cart.find(function (i) { return i.id === id; });
        if (found) { found.qty = Math.min(99, found.qty + 1); }
        else {
            cart.push({ id: id, name: d.name, price: d.priceNum === '' ? null : parseInt(d.priceNum, 10), lead: parseInt(d.lead || '2', 10), qty: 1 });
        }
        save(); renderCart(); toast('✔ ' + d.name + ' ditambahkan ke pesanan');
    }

    function maxLead() { return cart.reduce(function (m, i) { return Math.max(m, i.lead || 2); }, 2); }

    function renderCart() {
        var box = $('cartBox');
        var count = cart.reduce(function (s, i) { return s + i.qty; }, 0);
        $('cartCount').textContent = count;

        if (!cart.length) {
            box.innerHTML = '<h3>Pesananmu</h3><div class="cart-empty">Keranjang masih kosong. <a href="#menu">Pilih menu dulu →</a></div>';
        } else {
            var total = 0, custom = false, html = '<h3>Pesananmu</h3>';
            cart.forEach(function (i, idx) {
                var sub = i.price === null ? null : i.price * i.qty;
                if (sub === null) custom = true; else total += sub;
                html += '<div class="cart-row">' +
                    '<div><span class="cn">' + escapeHtml(i.name) + '</span><span class="cp">' + (i.price === null ? 'Harga menyusul' : rupiah(i.price)) + '</span></div>' +
                    '<div class="qty"><button type="button" data-act="dec" data-i="' + idx + '">−</button><span>' + i.qty + '</span><button type="button" data-act="inc" data-i="' + idx + '">+</button></div>' +
                    '<div class="cs">' + (sub === null ? '-' : rupiah(sub)) + '</div>' +
                    '<button type="button" class="cx" data-act="del" data-i="' + idx + '" aria-label="Hapus">✕</button></div>';
            });
            html += '<div class="cart-total"><span>Total</span><span>' + rupiah(total) + '</span></div>';
            if (custom) html += '<div class="cart-note">* Ada item dengan harga menyusul (mis. Birthday Brownie), dikonfirmasi lewat WhatsApp.</div>';
            box.innerHTML = html;
        }
        updatePickupMin();
    }

    function escapeHtml(s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]; }); }

    $('cartBox').addEventListener('click', function (e) {
        var b = e.target.closest('[data-act]'); if (!b) return;
        var i = parseInt(b.dataset.i, 10);
        if (b.dataset.act === 'inc') cart[i].qty = Math.min(99, cart[i].qty + 1);
        if (b.dataset.act === 'dec') cart[i].qty = Math.max(1, cart[i].qty - 1);
        if (b.dataset.act === 'del') cart.splice(i, 1);
        save(); renderCart();
    });

    // ---------- Tanggal ambil: hanya Sabtu/Minggu, minimal H-n ----------
    var pickup = $('pickupDate');
    function ymd(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
    function earliestPickup() {
        var d = new Date(); d.setHours(0, 0, 0, 0); d.setDate(d.getDate() + maxLead());
        while (d.getDay() !== 6 && d.getDay() !== 0) d.setDate(d.getDate() + 1);
        return d;
    }
    function fmtLong(d) { return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); }
    function updatePickupMin() {
        var e = earliestPickup(), lead = maxLead();
        pickup.min = ymd(e);
        if (pickup.value && (pickup.value < pickup.min || ![0, 6].includes(new Date(pickup.value + 'T00:00:00').getDay()))) pickup.value = '';
        $('pickupHint').innerHTML = 'Pengambilan hanya hari <b>Sabtu &amp; Minggu</b>. Pesan minimal <b>H-' + lead + '</b>' + (lead > 2 ? ' (ada Birthday Brownie)' : '') + '. Paling cepat: <b>' + fmtLong(e) + '</b>.';
    }
    pickup.addEventListener('change', function () {
        if (!pickup.value) return;
        var day = new Date(pickup.value + 'T00:00:00').getDay();
        if (day !== 6 && day !== 0) { showError('Pengambilan pesanan hanya bisa hari Sabtu atau Minggu.'); pickup.value = ''; }
        else if (pickup.value < pickup.min) { showError('Tanggal terlalu dekat. Paling cepat ' + fmtLong(earliestPickup()) + '.'); pickup.value = ''; }
        else showError('');
    });

    // ---------- Popup detail ----------
    var modal = $('pmodal'), currentCard = null;
    function openModal(card) {
        currentCard = card;
        var d = card.dataset;
        $('pmImg').innerHTML = d.img ? '<img src="' + d.img + '" alt="">' : d.emoji;
        $('pmName').textContent = d.name;
        $('pmFlavor').textContent = d.flavor;
        $('pmDesc').textContent = d.desc;
        $('pmPrice').textContent = d.price === 'DM' ? 'Harga menyusul (DM)' : 'Rp ' + d.price;
        $('pmBadge').textContent = d.badge || '';
        $('pmBadge').style.display = d.badge ? 'inline-block' : 'none';
        modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
    grid.addEventListener('click', function (e) {
        var card = e.target.closest('.product-card'); if (!card) return;
        if (e.target.closest('.add-cart')) { addToCart(card); return; }
        openModal(card);
    });
    $('pmClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
    $('pmCta').addEventListener('click', function () { if (currentCard) addToCart(currentCard); closeModal(); });

    // ---------- Kirim pesanan ----------
    function showError(msg) {
        var el = $('orderError'); el.textContent = msg; el.classList.toggle('show', !!msg);
        if (msg) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    $('orderSubmit').addEventListener('click', function () {
        var f = $('orderForm'), btn = this;
        var v = function (n) { return f.elements[n].value.trim(); };
        showError('');
        if (!cart.length) return showError('Keranjang masih kosong, pilih menu dulu.');
        if (!v('name')) return showError('Nama wajib diisi.');
        if (!v('whatsapp')) return showError('Nomor WhatsApp wajib diisi.');
        if (!v('pickup_date')) return showError('Pilih tanggal ambil (Sabtu atau Minggu).');
        if (v('delivery_method') !== 'Walk-in Pickup' && !v('address')) return showError('Alamat wajib diisi untuk metode ini.');

        btn.disabled = true; btn.textContent = 'Mengirim...';
        fetch('/order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({
                name: v('name'), whatsapp: v('whatsapp'), pickup_date: v('pickup_date'),
                delivery_method: v('delivery_method'), address: v('address'), notes: v('notes'),
                items: cart.map(function (i) { return { id: i.id, qty: i.qty }; })
            })
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
          .then(function (res) {
              if (!res.ok) {
                  var msg = res.j.message || 'Gagal mengirim pesanan.';
                  if (res.j.errors) msg = Object.values(res.j.errors)[0][0];
                  showError(msg); return;
              }
              cart = []; save(); renderCart(); f.reset(); updatePickupMin();
              var ok = document.createElement('div');
              ok.className = 'order-success';
              ok.textContent = 'Pesanan ' + res.j.code + ' tersimpan! Lanjutkan kirim pesan di WhatsApp ya 🍪';
              $('orderError').parentNode.insertBefore(ok, $('orderError'));
              setTimeout(function () { ok.remove(); }, 8000);
              var w = window.open(res.j.wa_url, '_blank');
              if (!w) window.location.href = res.j.wa_url;
          })
          .catch(function () { showError('Koneksi bermasalah, coba lagi.'); })
          .finally(function () { btn.disabled = false; btn.textContent = 'Send Order via WhatsApp 🍪'; });
    });

    // ---------- Kirim komentar ----------
    $('reviewSubmit').addEventListener('click', function () {
        var f = $('reviewForm'), btn = this, msg = $('reviewMsg');
        msg.className = 'form-msg'; msg.textContent = '';
        btn.disabled = true;
        fetch('/reviews', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({
                name: f.elements.name.value.trim(), stars: f.elements.stars.value,
                body: f.elements.body.value.trim(), website: f.elements.website.value
            })
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
          .then(function (res) {
              if (res.ok) { msg.textContent = res.j.message; f.reset(); }
              else { msg.className = 'form-msg err'; msg.textContent = res.j.errors ? Object.values(res.j.errors)[0][0] : (res.j.message || 'Gagal mengirim.'); }
          })
          .catch(function () { msg.className = 'form-msg err'; msg.textContent = 'Koneksi bermasalah, coba lagi.'; })
          .finally(function () { btn.disabled = false; });
    });

    // ---------- Scroll reveal ----------
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(function (n) { io.observe(n); });
    cards.forEach(function (c) { c.classList.add('reveal'); io.observe(c); });

    renderCart();
})();
