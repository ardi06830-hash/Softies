<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Softie's Cookie — Soft Cookies &amp; Brownie Bites, Kediri</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- NAV -->
<nav>
    <a href="#home" class="nav-logo">Softie<span>'s</span></a>
    <ul class="nav-links">
        <li><a href="#menu">Menu</a></li>
        <li><a href="#reviews">Reviews</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#order">Order</a></li>
    </ul>
    <a href="#order" class="nav-cta">Order Now</a>
</nav>

<!-- HERO -->
<section id="home">
    <div class="checker-strip top"></div>
    <div class="hero-left">
        <span class="hero-tag">🍪 Kediri's Fave Soft Cookies</span>
        <h1 class="hero-h1">Soft.<br>Chewy.<br><em>Irresistible.</em></h1>
        <p class="hero-sub">Handcrafted <strong>soft cookies</strong> and fudgy <strong>brownie bites</strong> made fresh in Kediri. Starting from <strong>Rp 5k</strong> — because good things shouldn't be expensive.</p>
        <div class="hero-btns">
            <a href="#menu" class="btn-primary">See The Menu 🍪</a>
            <a href="#order" class="btn-secondary">Order via WA</a>
        </div>
        <div class="hero-badges">
            <div class="badge"><span class="badge-num">10+</span><span class="badge-label">Cookie Flavors</span></div>
            <div class="badge"><span class="badge-num">8+</span><span class="badge-label">Brownie Variants</span></div>
            <div class="badge"><span class="badge-num">5k</span><span class="badge-label">Starting From</span></div>
        </div>
    </div>
    <div class="hero-right">
        <div class="hero-cookie-grid">
            @foreach ($featured as $p)
                <div class="cookie-card-mini {{ $loop->first ? 'featured' : '' }}">
                    <span class="ccm-emoji">{{ $p->emoji ?: $p->category_emoji }}</span>
                    <div class="ccm-name">{{ $p->hero_name ?: $p->name }}</div>
                    <div class="ccm-price">{{ $p->featured_price }}</div>
                    @if ($p->hero_desc)<div class="ccm-desc">{{ $p->hero_desc }}</div>@endif
                </div>
            @endforeach
        </div>
    </div>
    <div class="checker-strip"></div>
</section>

<!-- MENU -->
<section id="menu">
    <div class="section-header reveal">
        <span class="section-tag">Our Menu</span>
        <h2 class="section-title">Pick Your <span>Favorite</span></h2>
        <p class="section-sub">All cookies baked fresh. Brownies packed in 7 bites — perfect for sharing or eating alone, we won't judge.</p>
    </div>

    <div class="menu-tabs">
        <button class="tab-btn active" data-filter="all">Semua</button>
        <button class="tab-btn" data-filter="cookie">🍪 Soft Cookies</button>
        <button class="tab-btn" data-filter="brownie">🍫 Brownies Bites</button>
        <button class="tab-btn" data-filter="scoop">🍨 Scoop Cookies</button>
    </div>

    <div class="brownie-note hidden" id="brownieNote">
        <strong>🍫 Brownie Bites — 7 per pack</strong>
        <br>Shelf life 4–5 days sealed. Suitable for nationwide shipping via Shopee. All packs contain 7 generous bites.
    </div>

    <div class="products-grid" id="productsGrid">
        @foreach ($products as $p)
            @php $isCustom = $p->price_label === 'DM'; @endphp
            <div class="product-card {{ $p->is_premium ? 'premium' : '' }}"
                 data-category="{{ $p->category }}"
                 data-name="{{ $p->name }}"
                 data-flavor="{{ $p->subtitle }}"
                 data-desc="{{ $p->description }}"
                 data-price="{{ $p->price_label }}"
                 data-badge="{{ $p->badge }}"
                 data-img="{{ $p->image_url }}"
                 data-emoji="{{ $p->category_emoji }}"
                 data-id="{{ $p->id }}"
                 data-price-num="{{ $p->price }}"
                 data-lead="{{ $p->lead_days }}">
                <div class="product-img-wrap">
                    @if ($p->image_url)
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" loading="lazy">
                    @else
                        {{ $p->category_emoji }}
                    @endif
                    <span class="product-badge-price">{{ $p->price_label }}</span>
                    @if ($p->badge)<span class="product-badge-hot">{{ $p->badge }}</span>@endif
                </div>
                <div class="product-info">
                    <div class="product-name">{{ $p->name }}</div>
                    <div class="product-flavor">{{ $p->subtitle }}</div>
                    <div class="product-desc">{{ $p->description }}</div>
                    @if ($p->lead_days > 2)<div class="lead-note">📅 Pesan minimal H-{{ $p->lead_days }}</div>@endif
                    <button type="button" class="product-cta add-cart">+ Tambah ke Pesanan</button>
                </div>
            </div>
        @endforeach
    </div>
    <p class="empty-note hidden" id="emptyNote">Segera hadir! 🍨 Menu kategori ini sedang disiapkan.</p>
</section>

<!-- REVIEWS -->
<section id="reviews">
    <div class="section-header reveal" style="position:relative;z-index:1;">
        <span class="section-tag">Honest Reviews</span>
        <h2 class="section-title">What <span>Customers</span> Say</h2>
        <p class="section-sub">100% real WhatsApp messages from actual customers. No filter, just love.</p>
        <div class="rating-bar">
            <span class="stars">⭐⭐⭐⭐⭐</span>
            <span class="rating-text">5.0</span>
            <span class="rating-count">from 50+ happy customers</span>
        </div>
    </div>
    <div class="reviews-masonry">
        @foreach ($reviews as $r)
            <div class="review-card {{ $r->highlight ? 'review-highlight' : '' }} reveal">
                <div class="review-stars">{{ str_repeat('⭐', $r->stars) }}</div>
                <div class="review-text">"{{ $r->body }}"</div>
                <div class="review-meta"><span class="review-dot"></span>{{ $r->author }}</div>
            </div>
        @endforeach
    </div>
    <div class="review-form-wrap">
        <form id="reviewForm" class="review-form" onsubmit="return false;">
            <h3>Tulis komentarmu ✍️</h3>
            <p>Komentar akan tampil setelah disetujui admin. Nama boleh dikosongkan (anonim).</p>
            <input type="text" name="name" class="rf-input" maxlength="40" placeholder="Nama (opsional)">
            <select name="stars" class="rf-input">
                <option value="5">⭐⭐⭐⭐⭐ — Enak banget</option>
                <option value="4">⭐⭐⭐⭐ — Enak</option>
                <option value="3">⭐⭐⭐ — Lumayan</option>
                <option value="2">⭐⭐ — Kurang</option>
                <option value="1">⭐ — Tidak suka</option>
            </select>
            <textarea name="body" class="rf-input" rows="3" maxlength="500" placeholder="Ceritakan pengalamanmu..."></textarea>
            <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button type="button" class="btn-primary" id="reviewSubmit">Kirim Komentar 💌</button>
            <div class="form-msg" id="reviewMsg"></div>
        </form>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="about-visual">
        <div class="checker-box">
            <div class="checker-box-inner">
                <span class="big-emoji">🍪</span>
                <h3>SOFTIE'S</h3>
                <p>Kediri's Soft Cookie</p>
            </div>
        </div>
    </div>
    <div class="about-content reveal">
        <span class="section-tag">About Us</span>
        <h2 class="about-title">Baked with <span>love</span>,<br>sold with <span>soul.</span></h2>
        <p class="about-text">Softie's Cookie was born from a simple belief: soft, chewy, and affordable cookies should be accessible to everyone. We handcraft every batch with quality ingredients, obsessive attention to texture, and flavors that genuinely hit different.</p>
        <p class="about-text">From our home kitchen in Kediri to your hands — each cookie and brownie bite is made fresh, packed with care, and worth every rupiah.</p>
        <div class="about-info">
            <div class="info-row"><span class="info-icon">📍</span><span class="info-label">Location</span><span>Jl. Dhoho, Kota Kediri / Kandat, Kab. Kediri, GG Masjid Ngadiluwih</span></div>
            <div class="info-row"><span class="info-icon">📱</span><span class="info-label">WhatsApp</span><a href="https://wa.me/6285606685163" style="color:var(--blue);font-weight:700;">0856-0668-5163</a></div>
            <div class="info-row"><span class="info-icon">📸</span><span class="info-label">Instagram</span><a href="https://instagram.com/softies.cookie" target="_blank" style="color:var(--blue);font-weight:700;">@softies.cookie</a></div>
            <div class="info-row"><span class="info-icon">🚗</span><span class="info-label">Delivery</span><span>Local delivery + walk-in pickup. Brownies can ship nationwide via Shopee.</span></div>
            <div class="info-row"><span class="info-icon">🕐</span><span class="info-label">Shelf Life</span><span>Cookies: 2–3 days fresh. Brownies: 4–5 days sealed.</span></div>
        </div>
    </div>
</section>

<!-- ORDER -->
<section id="order">
    <div class="section-header reveal">
        <span class="section-tag">Order Now</span>
        <h2 class="section-title">Let's Get You <span>Some Cookies</span></h2>
        <p class="section-sub">Pilih menu, masukkan ke keranjang, lalu kirim pesanan. Kami konfirmasi lewat WhatsApp dalam 1–2 jam.</p>
    </div>
    <div class="order-wrap">
        <div class="order-card reveal">
            <h2>Place Your Order</h2>

            <div class="cart-box" id="cartBox"></div>

            <form id="orderForm" onsubmit="return false;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="name" class="form-input" placeholder="e.g. Rani">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="tel" name="whatsapp" class="form-input" placeholder="e.g. 08123456789">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Ambil</label>
                        <input type="date" name="pickup_date" class="form-input" id="pickupDate">
                        <div class="form-hint" id="pickupHint">Pengambilan hanya hari <b>Sabtu &amp; Minggu</b>. Pesan minimal H-2 (Birthday Brownie H-5).</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Delivery Method</label>
                        <select name="delivery_method" class="form-select">
                            <option>Walk-in Pickup</option>
                            <option>Local Delivery (Kediri)</option>
                            <option>Shopee Shipping (Brownies only)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Delivery Address (jika bukan Walk-in)</label>
                    <input type="text" name="address" class="form-input" placeholder="Your address in Kediri...">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes / Special Requests</label>
                    <textarea name="notes" class="form-textarea" placeholder="Anything else? Custom packaging, birthday message, allergies..."></textarea>
                </div>
                <div class="form-error" id="orderError"></div>
                <button type="button" class="form-submit" id="orderSubmit">Send Order via WhatsApp 🍪</button>
            </form>
            <div class="order-wa">
                <p>Or skip the form and chat directly:</p>
                <a href="https://wa.me/6285606685163?text=Hii kak! Mau order Softie's Cookie nih 🍪" target="_blank" class="wa-btn"><span>💬</span> Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div>
            <div class="footer-brand-name">Softie<span>'s</span> Cookie</div>
            <div class="footer-tagline">Soft. Chewy. Worth every rupiah. 🍪</div>
            <a href="https://instagram.com/softies.cookie" target="_blank" class="footer-ig">📸 @softies.cookie</a>
        </div>
        <div>
            <div class="footer-col-title">Quick Links</div>
            <ul class="footer-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#reviews">Reviews</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#order">Order</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-col-title">Contact</div>
            <ul class="footer-links">
                <li><a href="https://wa.me/6285606685163">📱 0856-0668-5163</a></li>
                <li><a href="#">📍 Kediri, East Java</a></li>
                <li><a href="https://instagram.com/softies.cookie" target="_blank">📸 softies.cookie</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 Softie's Cookie · Made with 🍪 in Kediri, East Java · <a href="https://wa.me/6285606685163">Order Now</a></p>
    </div>
</footer>

<!-- POPUP DETAIL -->
<div class="pmodal" id="pmodal" aria-hidden="true">
    <div class="pmodal-box">
        <button type="button" class="pmodal-close" id="pmClose" aria-label="Tutup">✕</button>
        <div class="pmodal-img" id="pmImg"></div>
        <div class="pmodal-body">
            <span class="product-badge-hot pm-badge" id="pmBadge"></span>
            <h3 class="pm-name" id="pmName"></h3>
            <div class="product-flavor" id="pmFlavor"></div>
            <p class="pm-desc" id="pmDesc"></p>
            <div class="pm-price" id="pmPrice"></div>
            <button type="button" class="product-cta" id="pmCta">+ Tambah ke Pesanan</button>
        </div>
    </div>
</div>

<a href="#order" class="cart-fab" id="cartFab" aria-label="Keranjang">🛒 <span id="cartCount">0</span></a>
<div class="toast" id="toast"></div>

@auth
    <div class="admin-bar">
        <span>Mode Admin</span>
        <a href="{{ route('admin.dashboard') }}">← Kembali ke Admin</a>
    </div>
@endauth

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
