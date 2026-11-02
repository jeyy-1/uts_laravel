@extends('layouts.app')
@section('title', 'Katalog Produk Ritel & Belanja')

@section('content')
<!-- Header Katalog Produk -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Katalog Produk</h3>
        <p class="text-muted small mb-0">Pilih produk dan tambahkan ke keranjang untuk melakukan pemesanan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-primary d-flex align-items-center gap-2 position-relative px-3 py-2 rounded-3 shadow-xs"
                type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
            <i class="bi bi-cart3 fs-5"></i>
            <span class="fw-bold">Keranjang Belanja</span>
            <span class="badge bg-danger rounded-pill ms-1" id="navCartCountBadge">0</span>
        </button>
    </div>
</div>


<!-- Filter Kategori & Pencarian Modern -->
<div class="row g-3 mb-4 align-items-center" id="katalog-produk">
    <div class="col-md-7">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('home') }}" class="btn btn-sm {{ !request('category_id') ? 'btn-primary' : 'btn-outline-secondary bg-white' }} rounded-pill px-3 py-2 shadow-xs">
                Semua Kategori
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('home', ['category_id' => $cat->id, 'q' => request('q')]) }}"
                   class="btn btn-sm {{ request('category_id') == $cat->id ? 'btn-primary' : 'btn-outline-secondary bg-white' }} rounded-pill px-3 py-2 shadow-xs">
                    {{ $cat->name }} <span class="badge {{ request('category_id') == $cat->id ? 'bg-white text-primary' : 'bg-light text-secondary' }} ms-1 rounded-pill">{{ $cat->products_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="col-md-5">
        <form method="GET" action="{{ route('home') }}" class="d-flex gap-2">
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari nama produk atau SKU...">
                @if(request('q') || request('category_id'))
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary border-start-0" title="Reset filter"><i class="bi bi-x-lg"></i></a>
                @endif
                <button class="btn btn-primary px-4 fw-semibold" type="submit">Cari</button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Cart Button -->
<div class="position-fixed bottom-0 end-0 m-4 z-3">
    <button class="btn btn-primary btn-lg shadow-lg rounded-pill px-4 py-3 d-flex align-items-center gap-2 position-relative"
            type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"
            style="box-shadow: 0 10px 25px -3px rgba(79, 70, 229, 0.4) !important;">
        <i class="bi bi-cart-fill fs-5"></i>
        <span class="fw-bold">Keranjang Belanja</span>
        <span id="floatingCartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
            0
        </span>
    </button>
</div>

<!-- Grid Display Produk -->
<div class="row g-4 mb-5">
    @forelse ($products as $p)
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden product-card hover-lift">
                <!-- Badge Kategori -->
                <div class="position-absolute top-0 start-0 m-3 z-1">
                    <span class="badge bg-dark bg-opacity-75 backdrop-blur px-2 py-1 rounded-pill small" style="font-size: 0.75rem;">
                        {{ $p->category->name }}
                    </span>
                </div>

                <!-- Status Stok Dinamis -->
                <div class="position-absolute top-0 end-0 m-3 z-1">
                    @if ($p->stock <= 0)
                        <span class="badge badge-subtle-danger px-2 py-1 rounded-pill shadow-xs">
                            <i class="bi bi-x-circle-fill me-1"></i> Stok Habis
                        </span>
                    @elseif ($p->stock <= $p->minimum_stock)
                        <span class="badge badge-subtle-warning px-2 py-1 rounded-pill shadow-xs">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Sisa {{ $p->stock }} (Menipis)
                        </span>
                    @else
                        <span class="badge badge-subtle-success px-2 py-1 rounded-pill shadow-xs">
                            <i class="bi bi-check-circle-fill me-1"></i> Stok: {{ $p->stock }}
                        </span>
                    @endif
                </div>

                <!-- Gambar/Ikon Produk -->
                <div class="card-img-top p-4 text-center d-flex align-items-center justify-content-center"
                     style="height: 170px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #f1f5f9;">
                    <div class="p-3 bg-white rounded-circle shadow-xs">
                        <i class="bi bi-box-seam fs-1 text-primary opacity-75"></i>
                    </div>
                </div>

                <div class="card-body d-flex flex-column p-4">
                    <div class="text-muted small mb-1 font-mono" style="font-size: 0.75rem;">SKU: {{ $p->sku }}</div>
                    <h6 class="card-title fw-bold text-dark mb-1">{{ $p->name }}</h6>
                    <p class="card-text text-muted small flex-grow-1 mb-3" style="line-height: 1.45;">
                        {{ Str::limit($p->description ?: 'Produk ritel kualitas pilihan terjamin.', 75) }}
                    </p>

                    <div class="d-flex justify-content-between align-items-baseline mb-3 pt-2 border-top">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.75rem;">Harga Satuan:</span>
                            <span class="fs-5 fw-bold text-primary font-mono">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <span class="badge bg-light text-secondary border">Tersedia {{ $p->stock }}</span>
                    </div>

                    <!-- Tombol Masukkan Keranjang Multi-Barang -->
                    @if ($p->stock > 0)
                        <button type="button" class="btn btn-outline-primary w-100 fw-bold add-to-cart-btn rounded-3"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}"
                                data-sku="{{ $p->sku }}"
                                data-price="{{ $p->selling_price }}"
                                data-stock="{{ $p->stock }}">
                            <i class="bi bi-cart-plus me-1"></i> + Masukkan Keranjang
                        </button>
                    @else
                        <!-- Proteksi: Mencegah pembelian saat stok habis -->
                        <button type="button" class="btn btn-light text-muted w-100 fw-semibold disabled rounded-3 border" disabled>
                            <i class="bi bi-slash-circle me-1"></i> Stok Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 py-5 text-center">
            <div class="p-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="fw-bold mt-3 text-secondary">Tidak ada produk ditemukan</h5>
                <p class="text-muted">Coba kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">Lihat Semua Produk</a>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mb-5">
    {{ $products->links() }}
</div>

<!-- OFFCANVAS / DRAWER KERANJANG BELANJA (MULTI-BARANG CHECKOUT) -->
<div class="offcanvas offcanvas-end shadow-lg border-0" tabindex="-1" id="cartOffcanvas" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom bg-white py-3 px-4">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2 text-dark">
            <i class="bi bi-bag-check-fill text-primary"></i> Keranjang Belanja Pelanggan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-4">

        <!-- Daftar Item di Keranjang -->
        <div id="cartItemsContainer" class="flex-grow-1 overflow-auto mb-3 pe-1">
            <div id="emptyCartMessage" class="text-center py-5 text-muted">
                <i class="bi bi-cart-x fs-1 opacity-40 text-muted"></i>
                <p class="mt-2 mb-0">Keranjang masih kosong.<br>Silakan pilih produk dari etalase di samping.</p>
            </div>
            <ul id="cartList" class="list-group list-group-flush"></ul>
        </div>

        <!-- Form Checkout Pelanggan -->
        <form method="POST" action="{{ route('store.checkout') }}" id="checkoutForm" class="border-top pt-3">
            @csrf
            <!-- Hidden inputs container for multi-items -->
            <div id="hiddenCartInputs"></div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Nama Pembeli / Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control" required
                       value="{{ auth()->check() ? auth()->user()->name : old('customer_name', 'Pelanggan Ritel') }}"
                       placeholder="Contoh: Budi Santoso">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Pilih Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash">Tunai (Cash di Kasir)</option>
                    <option value="qris">QRIS Dinamis (BCA/Mandiri/GoPay/OVO)</option>
                    <option value="transfer">Transfer Bank Virtual Account</option>
                </select>
            </div>

            <!-- Ringkasan Total Belanja -->
            <div class="bg-light p-3 rounded-3 mb-3 border">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Jumlah Variasi Barang:</span>
                    <span id="cartTotalItemsCount" class="fw-bold text-dark">0 jenis</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Total Kuantitas:</span>
                    <span id="cartTotalUnitsCount" class="fw-bold text-dark">0 unit</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Total Pembayaran:</span>
                    <span id="cartTotalPrice" class="fs-4 fw-bold text-primary font-mono">Rp 0</span>
                </div>
            </div>

            <button type="submit" id="checkoutSubmitBtn" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3 py-3" disabled>
                <i class="bi bi-check-circle-fill me-1"></i> Checkout & Bayar Sekarang
            </button>
        </form>
    </div>
</div>

<!-- Toast Feedback Tambah Barang -->
<div class="position-fixed bottom-0 start-0 p-3 z-3">
    <div id="cartToast" class="toast align-items-center text-bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                <i class="bi bi-cart-check-fill text-success fs-5"></i>
                <span id="toastMessage" class="fw-semibold">Produk ditambahkan ke keranjang.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                const name = this.dataset.name;
                const sku = this.dataset.sku;
                const price = parseFloat(this.dataset.price);
                const maxStock = parseInt(this.dataset.stock);

                addToCart(id, name, sku, price, maxStock);
            });
        });

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang belanja Anda masih kosong.');
            }
        });
    });

    function addToCart(id, name, sku, price, maxStock) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty < maxStock) {
                existing.qty += 1;
                showToast(`+1 ${name} ditambahkan (${existing.qty} unit).`);
            } else {
                alert(`Maksimal stok tersedia untuk "${name}" hanya ${maxStock} unit.`);
                return;
            }
        } else {
            cart.push({
                id: id,
                name: name,
                sku: sku,
                price: price,
                qty: 1,
                maxStock: maxStock
            });
            showToast(`${name} berhasil dimasukkan ke keranjang.`);
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            cart = cart.filter(i => i.id !== id);
        } else if (newQty > item.maxStock) {
            alert(`Stok maksimal yang tersedia untuk "${item.name}" hanya ${item.maxStock} unit.`);
            return;
        } else {
            item.qty = newQty;
        }
        renderCart();
    }

    function removeItem(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function renderCart() {
        const cartList = document.getElementById('cartList');
        const emptyMessage = document.getElementById('emptyCartMessage');
        const hiddenInputs = document.getElementById('hiddenCartInputs');
        const submitBtn = document.getElementById('checkoutSubmitBtn');
        const badge = document.getElementById('floatingCartBadge');

        cartList.innerHTML = '';
        hiddenInputs.innerHTML = '';

        let totalUnits = 0;
        let totalPrice = 0;

        if (cart.length === 0) {
            emptyMessage.style.display = 'block';
            submitBtn.disabled = true;
            badge.innerText = '0';
        } else {
            emptyMessage.style.display = 'none';
            submitBtn.disabled = false;

            cart.forEach((item, index) => {
                totalUnits += item.qty;
                const subtotal = item.price * item.qty;
                totalPrice += subtotal;

                hiddenInputs.innerHTML += `
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                `;

                const li = document.createElement('li');
                li.className = 'list-group-item px-0 py-3 d-flex justify-content-between align-items-center gap-2 border-bottom';
                li.innerHTML = `
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark small mb-0">${item.name}</div>
                        <div class="text-muted small">${formatRupiah(item.price)} &times; ${item.qty} = <span class="fw-semibold text-primary font-mono">${formatRupiah(subtotal)}</span></div>
                        <div class="text-muted" style="font-size: 0.72rem;">Batas stok: ${item.maxStock} unit</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0 rounded-2" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="fw-bold px-2 small font-mono">${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0 rounded-2" onclick="updateQty(${item.id}, 1)">+</button>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-1 px-2 py-0 rounded-2" onclick="removeItem(${item.id})" title="Hapus item">&times;</button>
                    </div>
                `;
                cartList.appendChild(li);
            });

            badge.innerText = totalUnits;
        }

        document.getElementById('cartTotalItemsCount').innerText = cart.length + ' jenis produk';
        document.getElementById('cartTotalUnitsCount').innerText = totalUnits + ' unit';
        document.getElementById('cartTotalPrice').innerText = formatRupiah(totalPrice);
    }

    function showToast(msg) {
        document.getElementById('toastMessage').innerText = msg;
        const toastEl = document.getElementById('cartToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 2200 });
        toast.show();
    }
</script>
@endpush
